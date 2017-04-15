<?php

namespace PunchyRascal\DonkeyCms\Controller\Admin;

class ArticleStats extends Base {

	public function output() {
		$this->getTemplate()->setFileName('admin/articleStats.twig');

		$data = $this->db->query("SELECT heading, article.id, article.date, article.views
			FROM article
			ORDER BY article.date DESC
		");
		$max = $this->db->getColumn("SELECT MAX(views) AS max FROM article");

		$this->getTemplate()
			->setValue('items', $data)
			->setValue('max', $max);

		return parent::output();
	}

}
