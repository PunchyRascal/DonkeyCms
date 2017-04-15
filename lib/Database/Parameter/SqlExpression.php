<?php

namespace PunchyRascal\DonkeyCms\Database\Parameter;

class SqlExpression extends Parameter {

	private $value;

	public function __construct(\PunchyRascal\DonkeyCms\Database\Database $db, $value) {
		parent::__construct($db);
		$this->value = $value;
	}

	public function getSafeSql() {
		switch ($this->value) {
			case 'NOW()':
				return $this->value;
			default:
				throw new \InvalidArgumentException("$this->value is not an allowed SQL expression");
		}
	}

}
