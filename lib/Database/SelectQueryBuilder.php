<?php

namespace PunchyRascal\DonkeyCms\Database;

/**
 * Assembles an SQL query
 */
class SelectQueryBuilder {

	/**
	 * @var Database
	 */
	private $db;
	private $calcFoundRows = false;
	private $select = [];
	private $from;
	private $joins = [];
	private $where = [];
	private $orderBy = [];
	private $groupBy = [];
	private $limitFrom;
	private $limitCount;

	public function __construct(Database $db) {
		$this->db = $db;
	}

	public function calcFoundRows($enable = true) {
		$this->calcFoundRows = $enable;
		return $this;
	}

	public function select() {
		$this->select = array_merge($this->select, func_get_args());
		return $this;
	}

	public function resetSelect() {
		$this->select = [];
		return $this;
	}

	public function from($name, $alias = null) {
		$this->from = [$name, $alias];
		return $this;
	}

	public function innerJoin() {
		$this->joins[] = array_merge(['INNER'], func_get_args());
		return $this;
	}

	public function leftJoin() {
		$this->joins[] = array_merge(['LEFT'], func_get_args());
		return $this;
	}

	public function flagJoinAs($name) {
		$this->joins[$name] = array_pop($this->joins);
		return $this;
	}

	public function removeFlaggedJoin($name) {
		if (isset($this->joins[$name])) {
			unset($this->joins[$name]);
		}
		return $this;
	}

	public function where() {
		$this->where[] = func_get_args();
		return $this;
	}

	/**
	 * This can change where conditions order
	 */
	public function flagWhereAs($name) {
		$this->where[$name] = array_pop($this->where);
		return $this;
	}

	/**
	 * Remove a WHERE condition flagged with a name
	 * @param string $name Name of a flagged where condition
	 * @return \PunchyRascal\DonkeyCms\Database\SelectQueryBuilder
	 */
	public function removeFlaggedWhere($name) {
		if (isset($this->where[$name])) {
			unset($this->where[$name]);
		}
		return $this;
	}

	public function orderBy($orderBy) {
		$this->orderBy[] = $orderBy;
		return $this;
	}

	public function resetOrderBy() {
		$this->orderBy = [];
		return $this;
	}

	public function groupBy($groupBy) {
		$this->groupBy[] = $groupBy;
		return $this;
	}

	public function resetGroupBy() {
		$this->groupBy = [];
		return $this;
	}

	public function limitFrom($from) {
		$this->limitFrom = abs(intval($from));
		return $this;
	}

	public function limitCount($count) {
		$this->limitCount = abs(intval($count));
		return $this;
	}

	public function resetLimit() {
		$this->limitCount = null;
		$this->limitFrom = null;
		return $this;
	}

	public function __toString() {
		return $this->buildSql();
	}

	public function buildSql() {
		$sql = [];
		$this->processSelect($sql);
		$this->processFrom($sql);
		$this->processJoins($sql);
		$this->processWhere($sql);
		$this->processGroupBy($sql);
		$this->processOrderBy($sql);
		$this->processLimit($sql);
		return join(' ', $sql);
	}

	public function copy() {
		return clone $this;
	}

	public function getWhereSql() {
		$sql = [];
		$this->processWhere($sql);
		return join(' ', $sql);
	}

	private function processSelect(&$sql) {
		$sql[] = "\nSELECT";
		if ($this->calcFoundRows) {
			$sql[] = "SQL_CALC_FOUND_ROWS";
		}
		if (!$this->select) {
			$sql[] = "*";
		} else {
			$columns = [];
			foreach ($this->select AS $col) {
				$columns[] = $this->db->formatQuery(is_array($col) ? $col : [$col]);
			}
			$sql[] = join(', ', $columns);
		}
	}

	private function processFrom(&$sql) {
		$sql[] = "\nFROM";
		$sql[] = $this->db->escapeIdentifier($this->from[0]);
		if ($this->from[1]) {
			$sql[] = "AS";
			$sql[] = $this->db->escapeIdentifier($this->from[1]);
		}
	}

	private	function processJoins(&$sql) {
		foreach ($this->joins AS $join) {
			$sql[] = "\n$join[0] JOIN";
			array_shift($join);
			$sql[] = $this->db->formatQuery($join);
		}
	}

	private function processOrderBy(&$sql) {
		if ($this->orderBy) {
			$sql[] = "\nORDER BY";
		}
		foreach ($this->orderBy AS $orderBy) {
			$sql[] = $orderBy;
		}
	}

	private function processGroupBy(&$sql) {
		if ($this->groupBy) {
			$sql[] = "\nGROUP BY";
		}
		foreach ($this->groupBy AS $groupBy) {
			$parts = explode('.', $groupBy);
			$id = [];
			foreach ($parts AS $part) {
				$id[] = $this->db->escapeIdentifier($part);
			}
			$sql[] = join('.', $id);
		}
	}

	private function processLimit(&$sql) {
		if ($this->limitCount !== null AND $this->limitFrom !== null) {
			$sql[] = sprintf("\nLIMIT %d, %d", $this->limitFrom, $this->limitCount);
		}
		if ($this->limitCount !== null AND $this->limitFrom === null) {
			$sql[] = sprintf("\nLIMIT %d", $this->limitCount);
		}
		if ($this->limitCount === null AND $this->limitFrom !== null) {
			throw new Exception\QueryBuilderException("Cannot offset without limit.");
		}
	}

	private function processWhere(&$sql) {
		if ($this->where) {
			$sql[] = "\nWHERE";
			foreach ($this->where AS $where) {
				$sql[] = $this->db->formatQuery($where);
			}
		}
	}

}
