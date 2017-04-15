<?php

namespace PunchyRascal\DonkeyCms\Database\Parameter;

class InValues extends Parameter {

	/**
	 * @var array
	 */
	private $values;

	public function __construct(\PunchyRascal\DonkeyCms\Database\Database $db, array $values) {
		parent::__construct($db);
		$this->values = $values;
	}

	public function getSafeSql() {
		$escaped = [];
		foreach ($this->values AS $value) {
			$escaped[] = $this->db->escapeString($value, true);
		}
		return implode(',', $escaped);
	}

}
