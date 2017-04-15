<?php

namespace PunchyRascal\DonkeyCms\Database\Parameter;

abstract class Parameter {

	/**
	 * @var \PunchyRascal\DonkeyCms\Database\Database
	 */
	protected $db;

	public function __construct(\PunchyRascal\DonkeyCms\Database\Database $db) {
		$this->db = $db;
	}

	public function __toString() {
		return $this->getSafeSql();
	}

	abstract public function getSafeSql();

}
