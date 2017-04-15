<?php

namespace PunchyRascal\DonkeyCms\Controller;

use PunchyRascal\DonkeyCms\Http;
use PunchyRascal\DonkeyCms\Pager;
use PunchyRascal\DonkeyCms\Model\ArticleList as ArticleListModel;

class ArticleList extends Base {

	public function output() {
		return $this->cache->getByParams('articleList', [Http::getGet('p'), Http::getGet('from', 0)], function () {
			return $this->outputWithoutCache();
		});
	}

	public function outputWithoutCache() {
		$artModel = new ArticleListModel($this->db);
		$articles = $artModel->getList(Http::getGet('p', 'novinky'));
		$allCount = $artModel->allCount;
		$this->getTemplate()
			->setFileName('articleList.twig')
			->setValues([
				'pageTitle' => $this->getTitle(),
				'articles' => $articles,
				'paging' => new Pager(8, $allCount)
			]);
		return parent::output();
	}

	private function getTitle() {
		$title_tr = array(
			'' => '',
			'obyvatele' => 'Obyvatelé',
			'novinky' => 'Novinky',
		);
		return $title_tr[Http::getGet('p')];
	}

}
