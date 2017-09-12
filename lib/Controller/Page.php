<?php

namespace PunchyRascal\DonkeyCms\Controller;

use PunchyRascal\DonkeyCms\Http;

class Page extends Base {

	public function output() {
		$data = $this->getPageData();
		if (!$data) {
			Http::markResponseNotFound();
			$this->getTemplate()->setFileName('404.twig');
		} else {
			$this->getTemplate()
				->setFileName('page.twig')
				->setValues([
					'page' => $data,
					'pageTitle' => $data['name'],
				]);
		}
		return parent::output();
	}

	private function getPageData() {
		$data = $this->db->getRow(
			"SELECT * FROM e_page WHERE url = %s AND active = 1",
			Http::getGet('url')
		);
		return $data;
	}

}