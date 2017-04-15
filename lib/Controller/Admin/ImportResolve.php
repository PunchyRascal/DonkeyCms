<?php

namespace PunchyRascal\DonkeyCms\Controller\Admin;

use PunchyRascal\DonkeyCms\Http;
use PunchyRascal\DonkeyCms\Pager;
use PunchyRascal\DonkeyCms\Format;

class ImportResolve extends Base {

	public function execute() {
		if (Http::getPost('importResolveSubmit') AND is_array(Http::getPost('home'))) {
			foreach (Http::getPost('home') AS $product => $category) {
				if (!$category) {
					continue;
				}
				$this->db->query(
					"UPDATE e_item SET import_resolved = 1, active = 1, home = %s WHERE id = %s",
					$category,
					$product
				);
			}
			Http::redirect("?p=admin&action=importResolve&make=". Http::getPost('make'));
		}
	}

	public function output() {
		$this->getTemplate()->setFileName('admin/importResolve.twig');
		/**
		 * @todo remove HTML from controller
		 */
		$lineContent = ' <a data-name="%2$s" data-id=%1$d class="button btn btn-default btn-xs">vybrat</a>';

		$this->getTemplate()->setValue('lineContent', $lineContent);
		$this->getTemplate()->appendArray(array(
			'data' => $this->getItems(),
			'count' => $allCount = $this->db->foundRows(),
			'makes' => $this->db->query("SELECT DISTINCT(make) AS make FROM e_item WHERE import_resolved = 0 ORDER BY make"),
			'origins' => $this->db->query("SELECT DISTINCT(origin) AS origin FROM e_item WHERE import_resolved = 0 ORDER BY origin"),
			'cats' => \PunchyRascal\DonkeyCms\Model\Category::getCategoryTree(
				$this->cache,
				$this->db,
				[
					'page' => 'admin',
					'lineContent' => $lineContent
				]
			),
			'currentMake' => Http::getGet('make'),
			'currentOrigin' => Http::getGet('origin'),
			'paging' => new Pager(10, $allCount, [
				'action' => 'importResolve',
				'make' => Http::getGet('make'),
				'origin' => Http::getGet('origin'),
			])
		));
		return parent::output();
	}

	/**
	 * @todo use query builder
	 */
	private function getItems() {
		$where = "";
		if (Http::getGet('make')) {
			$where .= sprintf(" AND make = '%s'", $this->db->escapeString(Http::getGet('make')));
		}
		if (Http::getGet('origin')) {
			$where .= sprintf(" AND origin = '%s'", $this->db->escapeString(Http::getGet('origin')));
		}
		return $this->db->query(
			"SELECT SQL_CALC_FOUND_ROWS * FROM e_item WHERE import_resolved = 0 $where LIMIT "
			. intval(Http::getGet('from')) . ", 10"
		);
	}

}
