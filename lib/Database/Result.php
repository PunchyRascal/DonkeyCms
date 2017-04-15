<?php

namespace PunchyRascal\DonkeyCms\Database;

/**
 * DB query result
 */
class Result implements \IteratorAggregate, \ArrayAccess {

	private $indexBy;

	/**
	 * @var Database
	 */
	private $db;

	private $rows = [];

	private $queryArgs;

	public function __construct(Database $db, array $queryArgs) {
		$this->db = $db;
		$this->queryArgs = $queryArgs;
	}

	public function indexBy($indexBy) {
		$this->indexBy = $indexBy;
		return $this;
	}

	private function fetchData() {
		if (!$this->rows) {
			$this->prepareData();
		}
	}

	public function getIterator() {
		$this->fetchData();
		return new \ArrayIterator($this->rows);
	}

	private function prepareData() {
		$result = call_user_func_array([$this->db, 'performRawQuery'], $this->queryArgs);
		while ($row = $result->fetch_assoc()) {
			$this->rows[$row[$this->indexBy]] = $row;
		}
	}

	public function offsetExists($offset) {
		$this->fetchData();
		return isset($this->rows[$offset]);
	}

	public function offsetGet($offset) {
		$this->fetchData();
		return $this->rows[$offset];
	}

	public function offsetSet($offset, $value) {

	}

	public function offsetUnset($offset) {
		unset($this->rows[$offset]);
	}
}