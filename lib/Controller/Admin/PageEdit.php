<?php

namespace PunchyRascal\DonkeyCms\Controller\Admin;

use PunchyRascal\DonkeyCms\Http;

class PageEdit extends Base {

	public function execute() {
		if (!Http::getPost('pageEditSubmit')) {
			return;
		}

		try {
			if (Http::getPost('id')) {
				$this->db->query(
					"UPDATE e_page SET name = %s, active = %s, text = %s, url = %s WHERE id = %s",
					Http::getPost('name'),
					Http::getPost('active', 0),
					Http::getPost('text'),
					Http::getPost('url'),
					Http::getPost('id')
				);
			}
			else {
				$this->db->query(
					"INSERT INTO e_page SET name = %s, active = %s, text = %s, url = %s, modified = NOW()",
					Http::getPost('name'),
					Http::getPost('active', 0),
					Http::getPost('text'),
					Http::getPost('url')
				);
			}
		} catch (\PunchyRascal\DonkeyCms\Database\Exception\Database $e) {
			if (stripos($e->getMessage(), 'Duplicate entry') !== false) {
				Http::redirect("/?p=admin&action=pages&m=18");
			}
			throw $e;
		}
		Http::redirect("/?p=admin&action=pages&m=1");
	}

	public function output() {
		$this->getTemplate()->setFileName('admin/pageEdit.twig');

		if (Http::getGet('id')) {
			$this->getTemplate()->setValue(
				'item',
				$this->db->getRow(
					"SELECT * FROM e_page WHERE id = %s", Http::getGet('id')
				)
			);
		}

		return parent::output();
	}

}
