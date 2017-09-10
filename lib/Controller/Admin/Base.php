<?php

namespace PunchyRascal\DonkeyCms\Controller\Admin;

use PunchyRascal\DonkeyCms\Http;
use PunchyRascal\DonkeyCms\Session;

abstract class Base extends \PunchyRascal\DonkeyCms\Controller\Base {

	public function execute() {
		if (Http::getPost('loginSubmit')) {
			$user = $this->app->db->getRow(
				"SELECT password FROM e_admin_user WHERE login = %s",
				Http::getPost('user')
			);

			if ($user AND $user['password'] AND password_verify(Http::getPost('passw'), $user['password'])) {
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
		$this->getTemplate()
			->setValue('isAdminLogged', Session::isAdminLogged())
			->setValue(
				'resolveCount',
				$this->db->getColumn("SELECT COUNT(*) FROM e_item WHERE import_resolved = 0")
			)
			->setValue(
				'resolvedCount',
				$this->db->getColumn("SELECT COUNT(*) FROM e_item WHERE import_resolved = 1")
			)
			->setValue('showMenu', Http::getGet('action') === null)
			->setValue('currentAction', Http::getGet('action'));

		$this->getTemplate()->setValue(
			'customLines',
			$this->db->getRows("SELECT * FROM donkey_controller ORDER BY sequence")
		);
	}

}
