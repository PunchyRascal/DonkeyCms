<?php

namespace PunchyRascal\DonkeyCms\Controller;

class Homepage extends Base {

	public function output() {
		$this->getTemplate()
				->setFileName('homepage.twig')
				->setValue('page', $this->db->getRow("SELECT * FROM e_page WHERE url = 'homepage' AND active = 1"));
		return parent::output();
	}

}