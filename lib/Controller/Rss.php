<?php

namespace PunchyRascal\DonkeyCms\Controller;

class Rss extends Base {

	public function output() {
		header("Content-type: application/xml; charset=utf-8");
		$this->getTemplate()
				->setFileName('rss.twig')
				->setValue('title', 'novinky')
				->setValue(
					'items',
					$this->db->query("SELECT heading,id,teaser,date,author FROM article ORDER BY date DESC LIMIT 10")
				);
		return parent::output();
	}

}
