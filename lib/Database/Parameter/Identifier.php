<?php

namespace PunchyRascal\DonkeyCms\Database\Parameter;

/**
 * DB identifier (column, table name...)
 */
class Identifier extends Parameter {

	private $value;

	public function __construct(\PunchyRascal\DonkeyCms\Database\Database $db, $value) {
		parent::__construct($db);
		$this->value = $value;
	}

	public function getSafeSql() {
		return $this->db->escapeIdentifier($this->value);
	}

}
