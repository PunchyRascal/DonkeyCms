<?php

namespace PunchyRascal\DonkeyCms\Controller\Admin;

use PunchyRascal\DonkeyCms\Http;
use PunchyRascal\DonkeyCms\Session;
use PunchyRascal\DonkeyCms\Encryption;

abstract class Base extends \PunchyRascal\DonkeyCms\Controller\Base {

	public function execute() {
		if (Http::getPost('loginSubmit')) {
			$userFound = $this->app->db->numRows(
				"SELECT id FROM e_admin_user WHERE login = %s AND password = %s",
				Http::getPost('user'),
				Encryption::hash(Http::getPost('passw'))
			);
			if ($userFound === 1) {
				session_regenerate_id();
				Session::set('adminLogged', true);
				$this->app->db->query(
					"UPDATE e_admin_user SET last_login_date = NOW() WHERE login = %s",
					Http::getPost('user')
				);
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
