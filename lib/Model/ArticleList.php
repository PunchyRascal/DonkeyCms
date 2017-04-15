<?php

namespace PunchyRascal\DonkeyCms\Model;

use PunchyRascal\DonkeyCms\Http;

class ArticleList {

	public $allCount;

	/**
	 * @var \PunchyRascal\DonkeyCms\Database\Database
	 */
	private $db;

	public function __construct(\PunchyRascal\DonkeyCms\Database\Database $db) {
		$this->db = $db;
	}

	public function getList($category) {
		$query = $this->db->createSelectQuery()
			->select("COUNT(img.home_art) AS image_count, COUNT(comment.id) AS comments, article.*")
			->calcFoundRows()
			->from('article')
			->leftJoin('comment ON article.id = comment.home')
			->leftJoin('art_image AS img ON img.home_art = article.id')
			->groupBy('article.id')
			->orderBy('date DESC')
			->where("category = %s", $category);

		$query->limitCount(8)->limitFrom(Http::getGet('from', 0));
		$return = $this->db->query($query);
		$this->allCount = $this->db->foundRows();

		return $return;
	}

}