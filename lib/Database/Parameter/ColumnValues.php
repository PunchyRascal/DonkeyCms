<?php

namespace PunchyRascal\DonkeyCms\Database\Parameter;

class ColumnValues extends Parameter {

	/**
	 * @var array
	 */
	private $values;

	public function __construct(\PunchyRascal\DonkeyCms\Database\Database $db, array $values) {
		parent::__construct($db);
		$this->values = $values;
	}

	public function add($name, $value) {
		$this->values[$name] = $value;
	}

	public function getSafeSql() {
		$sql = array();
		foreach ($this->values AS $column => $value) {
			if ($value instanceof Parameter) {
				/* @var $param Parameter */
				$sql[] = $this->db->escapeIdentifier($column) . ' = ' . $value->getSafeSql();
			}
			elseif (is_int($value)) {
				$sql[] = $this->db->escapeIdentifier($column) . ' = ' . $value;
			}
			elseif ($value === null) {
				$sql[] = $this->db->escapeIdentifier($column) . ' = NULL';
			}
			else {
				$sql[] = $this->db->escapeIdentifier($column) . ' = ' . $this->db->escapeString($value, true);
			}
		}
		return implode(', ', $sql);
	}

}
