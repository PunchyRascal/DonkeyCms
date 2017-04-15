<?php

namespace PunchyRascal\DonkeyCms;

use PunchyRascal\DonkeyCms\Http;

class ErrorHandler {

	private $showStoppers = array();
	private $loggedExceptions = array();
	private $canGoOns = array();
	private static $log = array();
	/**
	 * @var Application
	 */
	private $app;

	public function __construct(Application $app) {
		$this->app = $app;
	}

	/**
	 * Outputs logged data
	 * called upon shutdown
	 */
	public function output() {
		$this->handleFatalErrors();
		if (php_sapi_name() === "cli") {
			return $this->cliOutput();
		}
		if ($this->app->useProductionFeatures()) {
			return $this->productionOutput();
		}
		$this->develOutput();
	}

	private function getAllProblems() {
		return array_merge($this->showStoppers, $this->loggedExceptions, $this->canGoOns);
	}

	private function cliOutput() {
		$errors = $this->getAllProblems();
		if ($errors OR self::$log) {
			array_walk(self::$log, function (&$value) {$value = print_r($value, true);});
			array_walk($errors, function (&$value) {$value = substr($value, 0, 2000);});
			$c = new \Colors\Color();
			echo $c("Errors (".count($errors).")\n======\n")->red()->bold(), "\n";
			echo $c(join("\n-----------------------\n", $errors))->white(), "\n\n";
			echo $c("Dumped\n======\n")->bold()->blue(), "\n\n";
			foreach (self::$log AS $key => $val) {
				echo $c("$key => $val\n")->green();
			}
			echo $c("Memory: ". round(memory_get_peak_usage() / 1024 / 1024, 2). " MiB\n")->dark_gray();
			header_remove('Location');
		}
		$errors ? exit(1) : exit;
	}

	private function develOutput() {
		$errors = $this->getAllProblems();
		if ($errors OR self::$log) {
			if (ob_get_length()) {
				ob_clean();
			}
			array_walk(self::$log, function (&$value) {$value = print_r($value, true);});
			echo "<style type='text/css'>body {font-family: sans-serif;font-size: 15px;}</style>
				<h1 style='color:red;'>Debug</h1>";
			echo '<pre>', join('<hr>', $errors), '</pre>';
			echo "<hr>Memory: ", round(memory_get_peak_usage() / 1024 / 1024, 2), " MiB";
			header_remove('Location');
			exit;
		}
	}

	private function productionOutput() {
		$host = Http::getServer('HTTP_HOST');
		if ($this->showStoppers) {
			echo "<!doctype html>
				<head>
					<title>Error na $host</title>
					<meta charset='utf-8'>
					<meta name=robots content=noindex>
					<style>
						* {font-family: sans-serif;}
						body {border: 10px double #666;padding: 20px;width: 800px;
						margin: 20% auto 0 auto;text-align: center;border-radius: 3px;}
						p {margin:15px 0;padding:0}
					</style>
				</head>
				<body>
					<div>
					<h1>Nastala technická chyba</h1>
					<p>Chyba byla nahlášena a bude co nejdříve opravena.</p>
					<p>Váš $host</p>
					<p><a href='http://$host'>Vrátit se na homepage</a></p>
				</body>
			";
		}
		if ($this->getAllProblems()) {
			$this->reportToAdmin();
		}
	}

	private function reportToAdmin() {
		$host = Http::getServer('HTTP_HOST');
		$div = str_repeat('-', 80);
		mail(
			'nicolaibiker@gmail.com',
			"Error on $host",
			"Where it happened: "
				. Http::getServer('HTTP_HOST') . Http::getServer('REQUEST_URI')
				. "\n\n"
				. join("\n$div\n", $this->getAllProblems()) . "\n\nTime: " . date('j. n. Y. H:i:s')
				. "\n\nSession: " . print_r($_SESSION, true)
				. "\n\nUser agent: " . Http::getServer('HTTP_USER_AGENT')
				. "\nRefereer: " . Http::getServer('HTTP_REFERER'),
			"From: ". $this->app->config->emailFrom ."\r\n"
		);
	}

	/**
	 * Catches fatal errors
	 */
	private function handleFatalErrors() {
		$error = error_get_last();
		if ($error) {
			ob_clean();
			// can't be sure if it actually is fatal error
			$this->canGoOns[] = "Fatal Error?: $error[message]\n\nFile: $error[file]:$error[line]";
		}
	}

	/**
	 * Error handler
	 * @param int $errno
	 * @param string $errstr
	 * @param string $errfile
	 * @param int $errline
	 */
	public function errorHandler($errno, $errstr = null, $errfile = null, $errline = null) {
		//if error wasn't suppressed by @
		if (\error_reporting() !== 0) {
			$message = "$errstr ($errno)\n\nFile: $errfile:$errline";
			switch ($errno) {
				case \E_ERROR:
					$this->showStoppers[] = 'E_ERROR: ' . $message;
					exit;
					break;
				case \E_WARNING:
					$this->canGoOns[] = 'E_WARNING: ' . $message;
					break;
				case \E_NOTICE:
					$this->canGoOns[] = 'E_NOTICE: ' . $message;
					break;
				default:
					$this->showStoppers[] = 'Unknown error:' . $message;
					break;
			}
		}
		return true;
	}

	/**
	 * Exception handler
	 * @param \Throwable $e (no typehint for backwards compatibility with PHP 5)
	 */
	public function exceptionHandler($e) {
		$this->showStoppers[] = $this->prepareExceptionEntry($e);
	}

	/**
	 * Log exception
	 * @param \Exception $e
	 */
	public function logException(\Exception $e) {
		$this->loggedExceptions[] = $this->prepareExceptionEntry($e);
	}

	private function prepareExceptionEntry($e) {
		return $this->filterSensitiveData(print_r($e, true));
	}

	private function filterSensitiveData($input) {
		$dbConf = null;
		if ($this->app && $this->app->config) {
			$dbConf = $this->app->config->db;
		}
		if (!$dbConf) {
			return $input;
		}
		return strtr(
			$input,
			[
				"=> $dbConf->server" => '=> *****',
				"=> $dbConf->user" => '=> *****',
				$dbConf->password => '*****',
				"=> $dbConf->name" => '=> *****'
			]
		);
	}

}
