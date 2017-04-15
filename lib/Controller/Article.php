<?php

namespace PunchyRascal\DonkeyCms\Controller;

use PunchyRascal\DonkeyCms\Http;

class Article extends Base {

	use \PunchyRascal\DonkeyCms\Recaptcha\VerificationTrait;

	public $id;

	public function execute() {
		if (Http::getPost('commentSubmit')) {
			if ($this->checkCaptcha()) {
				$this->db->query(
					"INSERT INTO comment (name, date, home, email, text) VALUES (%s, NOW(), %s, %s, %s)",
					Http::getPost('name'),
					(int) Http::getPost('id'),
					Http::getPost('email'),
					Http::getPost('text')
				);
				Http::redirect("?p=art&aid=" . Http::getPost('id') . "&m=17");
			}
			Http::redirect("?p=art&m=13&aid=" . Http::getPost('id') . "&m=12");
		}
	}

	public function output() {
		$this->id = Http::getGet('aid');
		$this->db->query("UPDATE article SET views = views + 1 WHERE id = %s", $this->id);
		$this->getTemplate()->setFileName('article.twig');
		$article = $this->db->getRow("SELECT article.* FROM article WHERE article.id = %s", $this->id);

		$this->getTemplate()->data = array(
			'pageTitle' => $article['heading'],
			'art' => $article,
			'images' => $this->db->query(
				"SELECT * FROM art_image WHERE home_art = %s AND art_img_count > 1 ORDER BY art_img_count ASC",
				$this->id
			),
			'openGraphMeta' => [
				'url' => $this->app->config->appUrl . '/?p=art&aid=' . $this->id,
				'type' => 'article',
				'title' => $article['heading'],
				'description' => strip_tags($article['teaser']),
				'image' => $this->app->config->appUrl . '/article/' . $this->id . '_1.jpg'
			]
		);
		if ($article['enable_discussion']) {
			$this->getTemplate()->setValue(
				'comments',
				$this->db->query("SELECT * FROM comment WHERE home = %s ORDER BY date ASC", $this->id)
			);
		}
		return parent::output();
	}

}
