<?php

namespace PunchyRascal\DonkeyCms\Controller\Admin;

class Main extends Base {

	public function output() {
		$this->getTemplate()->setFileName('admin/base.twig');
		return parent::output();
	}

}