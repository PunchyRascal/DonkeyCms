<?php

namespace PunchyRascal\DonkeyCms\Controller;

class Error404 extends Base {

	public function output() {
		$this->getTemplate()->setFileName('404.twig');
		$this->getTemplate()->setValue('pageTitle', 'Stránka nenalezena');
		return parent::output();
	}

}
