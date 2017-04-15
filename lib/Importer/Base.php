<?php

namespace PunchyRascal\DonkeyCms\Importer;

abstract class Base {

	const TYPE_ZBOZI = 1;

	/**
	 * Parsed XML structure
	 * @var \SimpleXMLElement
	 */
	protected $xml, $stockXml;

	/**
	 * @var \PunchyRascal\DonkeyCms\Application
	 */
	protected $app;

	/**
	 * @var \PunchyRascal\DonkeyCms\Logger
	 */
	protected $log;

	/**
	 * XML remote location
	 * @var string
	 */
	public $url, $stockUrl;

	public $isValid = false, $isStockValid = false, $origin, $minProducts;

	public function __construct(\PunchyRascal\DonkeyCms\Application $app, \PunchyRascal\DonkeyCms\Logger $log, array $config) {
		$this->app = $app;
		$this->url = $config['url'];
		$this->stockUrl = $config['stockUrl'];
		$this->log = $log;
		$this->origin = $config['origin'];
		$this->minProducts = $config['minProducts'];
	}

	public function getXml() {
		if (!$this->xml) {
			$this->xml = $this->loadXml($this->url);
			$this->isValid = $this->xml !== null;
		}
		return $this->xml;
	}

	public function getStockXml() {
		if (!$this->stockXml) {
			$this->stockXml = $this->loadXml($this->stockUrl);
			$this->isStockValid = $this->stockXml !== null;
		}
		return $this->stockXml;
	}

	private function loadXml($url) {
		$handle = curl_init($url);
		curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 120);
		$source = curl_exec($handle);
		$error = curl_error($handle);
		if ($error) {
			$this->log->error("Error dowloading feed: $error");
		}
		curl_close($handle);
		if ($this->isValidXML($source)) {
			return new \SimpleXMLElement($source);
		}
		return null;
	}

	//check if xml is valid document
	private function isValidXML($source) {
		$doc = @simplexml_load_string($source);
		if ($doc) {
			return true; //this is valid
		}
		return false; //this is not valid
	}

	public function getAvailabilityDescription($item) {
		return 'Externí sklad, 2 - 3 dny';
	}

	public function getAvailabilityInDays($item) {
		return 3;
	}

	abstract public function getItemName($item);
	abstract public function getItemText($item);
	abstract public function getItemPrice($item);
	abstract public function getItemImgUrl($item);
	abstract public function getItemId($item);
	abstract public function getItemBrand($item);
	abstract public function getItemStock($item);

	abstract public function getProducts();
	public function getStocks() {}

	public function getItemSpecialStock($item) {}
	public function getItemSpecialId($item) {}

}