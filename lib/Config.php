<?php

namespace PunchyRascal\DonkeyCms;

/**
 * Read from JSON config file
 */
class Config {

	private $data;

	/**
	 * @param string $filename JSON config file path
	 */
	public function __construct($filename) {
		$this->data = json_decode(file_get_contents($filename));
	}

	public function __get($name) {
		if (property_exists($this->data, $name)) {
			return $this->data->$name;
		}
		return null;
	}

}
