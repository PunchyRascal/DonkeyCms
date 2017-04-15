<?php

namespace PunchyRascal\DonkeyCms\Database\Parameter;

class TextFuzzy extends Parameter {

	private $text;

	public function __construct(\PunchyRascal\DonkeyCms\Database\Database $db, $text) {
		parent::__construct($db);
		$this->text = $text;
	}

	public function getSafeSql() {
		return "'%" . $this->db->escapeString($this->text) . "%'";
	}

}
