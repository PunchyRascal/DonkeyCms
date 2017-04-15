<?php

namespace PunchyRascal\DonkeyCms\Model;

class Product {

	private static $instances = [];
	private $data;

	/**
	 * @var \PunchyRascal\DonkeyCms\Application
	 */
	private $app;

	public $id;

	/**
	 * @return Product
	 */
	public static function getInstace(\PunchyRascal\DonkeyCms\Application $app, $id) {
		$id = intval($id);
		if (!isset(self::$instances[$id])) {
			self::$instances[$id] = new self($app, $id);
		}
		return self::$instances[$id];
	}

	private function __construct(\PunchyRascal\DonkeyCms\Application $app, $id) {
		$this->id = $id;
		$this->app = $app;
		$this->fetchData();
	}

	private function fetchData() {
		$this->data = $this->app->db->getRow("SELECT * FROM e_item WHERE id = %s", $this->id);
	}

	public function getVariants() {
		if (!$this->data['variant']) {
			return [];
		}
		return explode(',', $this->data['variant']);
	}

}
