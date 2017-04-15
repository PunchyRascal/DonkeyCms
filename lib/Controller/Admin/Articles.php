<?php

namespace PunchyRascal\DonkeyCms\Controller\Admin;

use PunchyRascal\DonkeyCms\Http;
use PunchyRascal\DonkeyCms\Pager;

class Articles extends Base {

	public function execute() {
		if (Http::getPost('deleteId')) {
			$this->db->query("DELETE FROM article WHERE id = %s", Http::getPost('deleteId'));
			Http::redirect("/?p=admin&action=arts&m=1");
		}
	}

	public function output() {
		$this->getTemplate()->setFileName('admin/articles.twig');

		$items = $this->db->query(
			"SELECT SQL_CALC_FOUND_ROWS * FROM article ORDER BY date DESC LIMIT %s, 15",
			(int) Http::getGet('from')
		);
		$allCount = $this->db->foundRows();
		$translate = array(
			'novinky' => 'Novinky',
			'obyvatele' => 'Obyvatelé'
		);

		$this->getTemplate()->setValue('items', $items);
		$this->getTemplate()->setValue('cats', $translate);
		$this->getTemplate()->setValue('paging', new Pager(15, $allCount, ['action' => 'arts']));

		return parent::output();
	}

}
