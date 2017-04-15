<?php

namespace PunchyRascal\DonkeyCms\Controller\Admin;

use PunchyRascal\DonkeyCms\Http;

class ArticleEdit extends Base {

	public function execute() {
		if (Http::getPost('articleEditSubmit')) {

			$cols = $this->db->createParam('COLUMNS', [
				'author' => Http::getPost('author'),
				'category' => Http::getPost('art_category'),
				'heading' => Http::getPost('heading'),
				'teaser' => Http::getPost('teaser'),
				'art' => Http::getPost('article'),
				'enable_discussion' => (int) Http::getPost('enable_discussion', 0)
			]);

			if (Http::getPost('id')) {
				$this->db->query(
					"UPDATE article SET %s WHERE id = %s",
					$cols,
					(int) Http::getPost('id')
				);
			}
			else {
				$this->db->query("INSERT INTO article SET %s, date = NOW()", $cols);
			}
			Http::redirect("?p=admin&action=arts&m=1");
		}
	}

	public function output() {
		$this->getTemplate()->setFileName('admin/articleEdit.twig');

		if (Http::getGet('id')) {
			$this->getTemplate()->setValue(
				'item',
				$this->db->getRow("SELECT * FROM article WHERE id = %s", Http::getGet('id'))
			);
		}

		return parent::output();
	}

}
