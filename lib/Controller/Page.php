<?php

namespace PunchyRascal\DonkeyCms\Controller;

use PunchyRascal\DonkeyCms\Http;

class Page extends Base {

	public function output() {
		$this->getTemplate()->setFileName('page.twig');
		$this->getTemplate()->setValue('page', $this->getPageData());
		return parent::output();
	}

	private function getPageData() {
		$data = $this->db->getRow(
			"SELECT * FROM e_page WHERE url = %s AND active = 1",
			Http::getGet('url')
		);
		if (!$data) {
			Http::markResponseNotFound();
		}
		return $data;
	}

}