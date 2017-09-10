<?php

namespace PunchyRascal\DonkeyCms;

use PunchyRascal\DonkeyCms\Http;

class Application {

	/**
	 * @todo move to config
	 **/
	const VAT_RATE = 21;

	/**
	 * @var Database\Database
	 */
	public $db;

	/**
	 * Configuration file data
	 * @var Config
	 */
	public $config;

	/**
	 * @var ErrorHandler
	 */
	public $errorHandler;

	/**
	 * @var Dumper
	 */
	private $dumper;

	/**
	 * @var Logger
	 */
	private $log;

	/**
	 * @var Cache
	 */
	private $cache;

	 /**
	 * @var Router
	 */
	private $router;

	public function __construct(Config $config) {
		$this->setErrorHandler();

		$this->config = $config;

		$dbConf = $this->config->db;
		$this->db = new Database\Database(
			$dbConf->server,
			$dbConf->user,
			$dbConf->password,
			$dbConf->name
		);
	}

	/**
	 *
	 * @return \PunchyRascal\DonkeyCms\Router
	 */
	public function getRouter() {
		if (!$this->router) {
			$this->router = new Router($this);
		}
		return $this->router;
	}

	public function useProductionFeatures() {
		return $this->config->isProductionMode;
	}

	private function setErrorHandler() {
		$this->errorHandler = new ErrorHandler($this);
		register_shutdown_function([$this->errorHandler, 'output']);
		set_error_handler([$this->errorHandler, 'errorHandler']);
		set_exception_handler([$this->errorHandler, 'exceptionHandler']);
		ini_set('display_errors', false);
		error_reporting(0);
	}

	public function logRecoverableError(\Exception $e) {
		$this->errorHandler->logException($e);
	}

	public function getDumper() {
		if (!$this->dumper) {
			$this->dumper = new Dumper($this);
		}
		return $this->dumper;
	}

	public function getLog() {
		if (!$this->log) {
			$this->log = new Logger('app.log', 'APP');
		}
		return $this->log;
	}

	public function getCache() {
		if (!$this->cache) {
			$this->cache = new Cache($this);
		}
		return $this->cache;
	}

}
