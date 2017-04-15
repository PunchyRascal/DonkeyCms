<?php

namespace PunchyRascal\DonkeyCms\Controller\Admin;

use PunchyRascal\DonkeyCms\Http;

class ProductIssues extends Base {

	private $type;

	public function execute() {
		$this->type = Http::getGet('type', 'duplicates');
	}

	public function output() {
		$this->getTemplate()->data = array(
			'items' => $this->getProducts(),
			'extraCount' => $this->db->foundRows() - 50,
			'currentTab' => $this->type,
		);
		$this->getTemplate()->setFileName('admin/productIssues.twig');
		return parent::output();
	}

	private function getProducts() {
		switch ($this->type) {
			case 'duplicates':
				return $this->db->query("SELECT SQL_CALC_FOUND_ROWS prod.*, cat.name AS catName, catParent.name AS catParentName
					FROM e_item AS prod
					LEFT JOIN e_cat AS cat ON prod.home = cat.id
					LEFT JOIN e_cat AS catParent ON cat.parent_cat = catParent.id
					INNER JOIN e_item AS dup ON
						dup.name = prod.name
						AND dup.id != prod.id
						AND dup.import_resolved != 0
					WHERE prod.import_resolved != 0
					GROUP BY prod.name
					LIMIT 50");
			case 'noPrice':
				return $this->db->query("SELECT SQL_CALC_FOUND_ROWS prod.*, cat.name AS catName, catParent.name AS catParentName
					FROM e_item AS prod
					LEFT JOIN e_cat AS cat ON prod.home = cat.id
					LEFT JOIN e_cat AS catParent ON cat.parent_cat = catParent.id
					WHERE price = 0 AND prod.import_resolved != 0
					LIMIT 50");
			case 'noCategory':
				return $this->db->query("SELECT SQL_CALC_FOUND_ROWS prod.*, cat.name AS catName, catParent.name AS catParentName
					FROM e_item AS prod
					LEFT JOIN e_cat AS cat ON prod.home = cat.id
					LEFT JOIN e_cat AS catParent ON cat.parent_cat = catParent.id
					WHERE cat.id IS NULL AND prod.import_resolved != 0
					LIMIT 50");
		}
	}

}
