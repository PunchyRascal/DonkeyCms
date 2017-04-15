<?php

namespace PunchyRascal\DonkeyCms;

class Logger {

	const LEVEL_INFO = 'info';
	const LEVEL_ERROR = 'error';
	const TYPE_RETURN_OUTPUT = -999;

	private $filename, $title;

	public function __construct($filename = null, $title = 'Log') {
		$this->title = $title;

		if ($filename === self::TYPE_RETURN_OUTPUT) {
			$this->filename = self::TYPE_RETURN_OUTPUT;
		} elseif ($filename) {
			$this->filename = __DIR__ . "/../../../../logs/$filename";
			touch($this->filename);
		}
	}

	private function log($message, $level) {
		$message = $this->formatLogLine($message, $level);
		if ($this->filename === self::TYPE_RETURN_OUTPUT) {
			return $message;
		} elseif (!$this->filename) {
			echo $message;
		} elseif (!file_put_contents($this->filename, $message, FILE_APPEND)) {
			throw new \Exception("Could not write log message into '$this->filename'");
		}
	}

	public function info() {
		return $this->log($this->transformMessage(...func_get_args()), self::LEVEL_INFO);
	}

	public function error() {
		return $this->log($this->transformMessage(...func_get_args()), self::LEVEL_ERROR);
	}

	private function formatLogLine($message, $level) {
		return date("Y-m-d H:i:s")
				. ' ' . strtoupper($level)
				. ': ' . $message . "\n";
	}

	private function transformMessage(...$toPrint) {
		foreach ($toPrint AS &$value) {
			if (!is_scalar($value)) {
				$value = print_r($value, true);
			}
		}
		if (func_num_args() > 1) {
			return sprintf(array_shift($toPrint), ...$toPrint);
		}
		return $toPrint[0];
	}

}
