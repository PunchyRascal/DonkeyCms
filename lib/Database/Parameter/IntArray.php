<?php

namespace PunchyRascal\DonkeyCms\Database\Parameter;

class IntArray extends Parameter {

	private $values;

	public function __construct(\PunchyRascal\DonkeyCms\Database\Database $db, array $values) {
		parent::__construct($db);
		$this->values = $values;
	}

	public function getSafeSql() {
		$escaped = [];
		foreach ($this->values AS $value) {
			$escaped[] = intval($value);
		}
		return implode(',', $escaped);
	}

}
