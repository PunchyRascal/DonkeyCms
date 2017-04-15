<?php

namespace PunchyRascal\DonkeyCms\Controller\Admin;

use PunchyRascal\DonkeyCms\Http;

class PollEdit extends Base {

	public function execute() {
		if (Http::getPost('pollEditSubmit')) {

			$cols = $this->db->createParam('COLUMNS', [
				'question' => Http::getPost('quest'),
				'ans1' => Http::getPost('ans1'),
				'ans2' => Http::getPost('ans2'),
				'ans3' => Http::getPost('ans3'),
				'ans4' => Http::getPost('ans4'),
				'ans5' => Http::getPost('ans5'),
				'active' => Http::getPost('active')
			]);

			if (is_numeric(Http::getPost('id'))) {
				$this->db->query(
					"UPDATE e_poll SET %s WHERE id = %s",
					$cols,
					Http::getPost('id')
				);
			}
			else {
				$cols->add('date', $this->db->createParam('SQL', 'NOW()'));
				$this->db->query(
					"INSERT INTO e_poll SET %s",
					$cols
				);
			}
			Http::redirect("?p=admin&action=polls&m=1");
		}
	}

	public function output() {
		$this->getTemplate()->setFileName('admin/pollEdit.twig');

		if (Http::getGet('id')) {
			$this->getTemplate()->setValue(
				'item',
				$this->db->getRow("SELECT * FROM e_poll WHERE id = %s", Http::getGet('id'))
			);
		}

		return parent::output();
	}

}
