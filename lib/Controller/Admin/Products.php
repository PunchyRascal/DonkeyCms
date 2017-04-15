<?php

namespace PunchyRascal\DonkeyCms\Controller\Admin;

use PunchyRascal\DonkeyCms\Http;
use PunchyRascal\DonkeyCms\Session;
use PunchyRascal\DonkeyCms\Pager;

class Products extends Base {

	private $where;
	private $makeWhere;

	public function execute() {
		if (Http::getGet('productDelete') AND Http::getGet('id')) {
			$cat = $this->db->getColumn("SELECT home FROM e_item WHERE id = %s", Http::getGet('id'));
			$this->db->query("DELETE FROM e_item WHERE id = %s", Http::getGet('id'));
			Http::redirect("?p=admin&action=e_items&cat=$cat#contentAnchor");
		}
	}

	public function output() {
		$cat = $this->getCategoryData();
		$this->getTemplate()->data = array(
			'list' => $this->getProducts($cat),
			'count' => $allCount = $this->db->foundRows(),
			'makes' => $this->db->query("SELECT DISTINCT(make) AS make FROM "
				. "e_item WHERE e_item.active = 1 $this->makeWhere ORDER BY make"),
			'currentMake' => Http::getGet('make'),
			'showDiscountsHeading' => Http::getGet('sleva') == 1,
			'showCategoryHeading' => !!Http::getGet('cat'),
			'category' => $cat,
			'pageTitle' => isset($cat['name']) ? $cat['name'] : '',
			'message' => Http::getGet('m'),
			'sortbyId' => Session::get('sortby'),
			'filter' => Http::getGet('filter'),
			'from' => Http::getGet('from'),
			'paging' => new Pager(
				18, $allCount,
				[
					'action' => 'e_items',
					'cat' => Http::getGet('cat'),
					'sleva' => Http::getGet('sleva'),
					'filter' => Http::getGet('filter'),
					'make' => Http::getGet('make'),
					'eSearch' => Http::getGet('eSearch'),
				]
			)
		);
		$this->getTemplate()->setFileName('admin/productList.twig');
		return parent::output();
	}

	private function getCategoryData() {
		return $cat = $this->db->getRow(
			"SELECT cat.*, parent.name AS catParentName, parent.id AS catParentId
			FROM e_cat AS cat
			LEFT JOIN e_cat AS parent ON parent.id = cat.parent_cat
			WHERE cat.id = %s LIMIT 1",
			Http::getGet('cat')
		);
	}

	/**
	 * @todo Use sql query builder
	 * @param array $cat
	 * @return array
	 */
	private function getProducts(&$cat) {
		$sortbyId = Http::getGet('sortby', Session::get('sortby'));
		$filter = Http::getGet('filter');
		$where = "";
		Session::set('sortby', $sortbyId);

		if (!Http::getGet('cat')) {
			if (Http::getGet('sleva') == 1) {
				$where = 'AND discount > 0';
			} elseif (Http::getGet('eSearch')) {
				$term = $this->db->escapeString(trim(Http::getGet('eSearch')));
				$where = " AND
				(e_item.id = '$term' OR e_item.name LIKE '%%$term%%' OR
				e_item.`desc` LIKE '%%$term%%' OR
				e_item.make LIKE '%%$term%%')	";
			}
		} else {
			$where = 'AND home IN ('. $cat['children'].intval(Http::getGet('cat')) .") ";
		}

		if ($filter) {
			$where .= ' AND stock >= 1';
		}

		$this->makeWhere = $where;

		if (Http::getGet('make')) {
			$where .= sprintf(" AND make = '%s'", $this->db->escapeString(Http::getGet('make')));
		}

		if (Http::getGet('name')) {
			$where .= sprintf(" AND e_item.name = '%s'", $this->db->escapeString(Http::getGet('name')));
		}

		$this->where = $where;

		switch ($sortbyId) {
			default:
			case 1:
				$sortby = 'name ASC';
				break;
			case 2:
				$sortby = 'name DESC';
				break;
			case 3:
				$sortby = 'price ASC';
				break;
			case 4:
				$sortby = 'price DESC';
				break;
			case 5:
		}
		$query = "SELECT SQL_CALC_FOUND_ROWS e_item.*, img.home_art AS imgExists,
				e_item.discount / (e_item.price / 100) AS discountPercent,
				e_cat.name AS catName, parent.name AS catParentName, parent.id AS catParentId
			FROM e_item
			LEFT JOIN e_cat ON e_item.home = e_cat.id
			LEFT JOIN e_cat AS parent ON e_cat.parent_cat = parent.id
			LEFT JOIN item_image AS img ON img.home_art = e_item.id
			WHERE 1 $where
			GROUP BY e_item.id
			ORDER BY $sortby
			LIMIT ". intval(Http::getGet('from')) .", 18";

		return $this->db->query($query);
	}

}
