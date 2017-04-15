<?php

namespace PunchyRascal\DonkeyCms\Database;

use PunchyRascal\DonkeyCms\Format;

class Database {

	const PARAM_IDENTIFIER = 'ID';

	/**
	 * @var \mysqli
	 */
	private $connection;

	public $queryCount = 0;

	/**
	 * @var int
	 */
	private $foundRows;

	private $calcFoundRows = false;

	public function __construct($server, $user, $password, $database) {
		$this->connect($server, $user, $password, $database);
	}

	public function __destruct() {
		if ($this->connection) { // tests do not connect to db
			$this->connection->close();
		}
	}

	public function getInsertId() {
		return $this->connection->insert_id;
	}

	public function escapeString($s, $includeQuotes = false, $makeNull = false) {
		$e = $this->connection->escape_string($s);
		if ($makeNull AND !$e) {
			return 'NULL';
		}
		if ($includeQuotes) {
			$e = "'$e'";
		}
		return $e;
	}

	public function escapeIdentifier($name) {
		return '`' . preg_replace("#[^a-z0-9\_]#i", "", $name) . '`';
	}

	private function connect($server, $user, $password, $database) {
		$this->connection = new \mysqli($server, Format::unScramble($user), Format::unScramble($password), $database);
		if ($this->connection->connect_error) {
			throw new Exception\Database(
				'Connection Error (' . $this->connection->connect_errno . ') '
					. $this->connection->connect_error
			);
		}
		$this->connection->set_charset('utf8');
	}

	public function error() {
		return $this->connection->error;
	}

	public function numRows() {
		$this->queryCount++;
		$result = $this->connection->query($query = $this->formatQuery(func_get_args()));
		if ($result === false) {
			throw new Exception\Database("DB error in query \"$query\" - " . $this->error());
		}
		return $result->num_rows;
	}

	public function affectedRows() {
		return $this->connection->affected_rows;
	}

	public function query() {
		$result = call_user_func_array([$this, 'performRawQuery'], func_get_args());
		if ($this->calcFoundRows) {
			$this->foundRows = (int) $this->getColumn("SELECT FOUND_ROWS()");
			$this->calcFoundRows = false;
		}

		if (is_object($result)) {
			$rows = array();
			while ($row = $result->fetch_assoc()) {
				$rows[] = $row;
			}
			return $rows;
		}
		if ($result === false) {
			throw new Exception\Database("DB error in query: ". $this->error());
		}
		return $result;
	}

	/**
	 * @return \mysqli_result
	 * @throws Exception\Database
	 */
	public function performRawQuery() {
		$this->queryCount++;
		/* @var $result \mysqli_result */
		$result = $this->connection->query($query = $this->formatQuery(func_get_args()));

		/**
		 * This is very fragile solution, FIX!
		 */
		if (stripos($query, 'SQL_CALC_FOUND_ROWS') !== false) {
			$this->calcFoundRows = true;
		}

		if (is_object($result)) {
			return $result;
		}
		if ($result === false) {
			/**
			 * Error: 1451 SQLSTATE: 23000 (ER_ROW_IS_REFERENCED_2)
			 * Message: Cannot delete or update a parent row: a foreign key constraint fails (%s)
			 */
			if ($this->connection->errno === 1451) {
				throw new Exception\ForeignKey\ParentRowHasChildren("DB error - cannot update/remove parent row, it is referenced in child records: " . $this->error());
			}
			throw new Exception\Database("DB error in query \"$query\" - " . $this->error());
		}
		return $result;
	}

	public function selectIndexed() {
		return new Result($this, func_get_args());
	}

	public function getRow() {
		$this->queryCount++;
		/* @var $result \mysqli_result */
		$result = $this->connection->query($query = $this->formatQuery(func_get_args()));
		if (is_object($result)) {
			return $result->fetch_assoc();
		}
		if ($result === false) {
			throw new Exception\Database("DB error in query \"$query\" - " . $this->error());
		}
		return $result;
	}

	/**
	 * Returns single column value
	 * @param string $sql Sql to fetch the column value
	 * @return string
	 * @throws Exception\Database
	 */
	public function getColumn() {
		$this->queryCount++;
		/* @var $result \mysqli_result */
		$result = $this->connection->query($query = $this->formatQuery(func_get_args()));
		if (is_object($result)) {
			$row = $result->fetch_array(\MYSQLI_NUM);
			return $row[0];
		}
		if ($result === false) {
			throw new Exception\Database("DB error in query \"$query\" - " . $this->error());
		}
	}

	public function beginTransaction() {
		if (!$this->areTransactionsSupported()) {
			return;
		}
		if (!$this->connection->begin_transaction()) {
			throw new Exception\Database("Could not start transaction");
		}
	}

	public function rollbackTransaction() {
		if (!$this->areTransactionsSupported()) {
			return;
		}
		if (!$this->connection->rollback()) {
			throw new Exception\Database("Could not rollback transaction");
		}
	}

	public function commitTransaction() {
		if (!$this->areTransactionsSupported()) {
			return;
		}
		if (!$this->connection->commit()) {
			throw new Exception\Database("Could not commit transaction");
		}
	}

	public function createParam($paramType, $argument) {
		$args = func_get_args();
		switch ($paramType) {
			case 'IN':
				return new Parameter\InValues($this, $argument);
			case 'INT_ARRAY':
				return new Parameter\IntArray($this, $argument);
			case 'COLUMNS':
				return new Parameter\ColumnValues($this, $argument);
			case 'ID':
				return new Parameter\Identifier($this, $argument);
			case 'SQL':
				return new Parameter\SqlExpression($this, $argument);
			case 'TEXT_FUZZY':
				return new Parameter\TextFuzzy($this, $argument);
			default:
				throw new Exception\Database("Cannot create unknown parameter '$paramType'");
		}
	}

	public function formatQuery($args) {
		$query = array_shift($args);
		if (count($args) > 0) {
			$expandedQuery = $this->applyParametersToQuery($query, $args);
			if (!$expandedQuery) {
				throw new Exception\Database("Could not process query '$query'");
			}
			return $expandedQuery;
		}
		return $query;
	}

	private function applyParametersToQuery($query, $arguments) {
		$sql = array($query);
		foreach ($arguments AS $param) {
			if ($param instanceof Parameter\Parameter) {
				/* @var $param Parameter\Parameter */
				$sql[] = $param->getSafeSql();
			} elseif (is_int($param)) {
				$sql[] = $param;
			} elseif ($param === null) {
				$sql[] = 'NULL';
			} else {
				$sql[] = $this->escapeString($param, true);
			}
		}
		return call_user_func_array('sprintf', $sql);
	}

	private function areTransactionsSupported() {
		return PHP_VERSION_ID >= 50500;
	}

	public function foundRows() {
		return $this->foundRows;
	}

	public function createSelectQuery() {
		return new \PunchyRascal\DonkeyCms\Database\SelectQueryBuilder($this);
	}

}
