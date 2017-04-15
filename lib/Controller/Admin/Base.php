<?php

namespace PunchyRascal\DonkeyCms\Controller\Admin;

use PunchyRascal\DonkeyCms\Http;
use PunchyRascal\DonkeyCms\Session;
use PunchyRascal\DonkeyCms\Encryption;

abstract class Base extends \PunchyRascal\DonkeyCms\Controller\Base {

	public function execute() {
		if (Http::getPost('loginSubmit')) {
			if (Encryption::hash(Http::getPost('passw')) === $this->app->config->adminLogin->password
					AND Encryption::hash(Http::getPost('user')) === $this->app->config->adminLogin->user) {
				session_regenerate_id();
				Session::set('adminLogged', true);
				Http::redirect("/?p=admin");
			}
			Http::redirect("/?p=admin&m=24");
		}

		if (Http::getGet('logout') == 'logout') {
			Session::delete('adminLogged');
			session_destroy();
			Http::redirect("/?p=admin");
		}
	}

	public function output() {
		$this->setAdminCommonData();
		return parent::output();
	}

	private function setAdminCommonData() {
		$this->getTemplate()->setValue('isAdminLogged', Session::isAdminLogged());
		$this->getTemplate()->setValue(
			'resolveCount',
			$this->db->getColumn("SELECT COUNT(*) FROM e_item WHERE import_resolved = 0")
		);
		$this->getTemplate()->setValue(
			'resolvedCount',
			$this->db->getColumn("SELECT COUNT(*) FROM e_item WHERE import_resolved = 1")
		);
		$this->getTemplate()->setValue('showMenu', Http::getGet('action') === null);
		$this->getTemplate()->setValue('currentAction', Http::getGet('action'));
	}

}
