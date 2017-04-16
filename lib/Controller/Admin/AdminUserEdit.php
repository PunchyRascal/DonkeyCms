<?php

namespace PunchyRascal\DonkeyCms\Controller\Admin;

use PunchyRascal\DonkeyCms\Http;

class AdminUserEdit extends Base {

	public function execute() {
		/**
		 * @todo: handle duplicate login exception
		 */
		if (Http::getPost('adminUserEditSubmit')) {

			$cols = $this->db->createParam('COLUMNS', [
				'login' => Http::getPost('login'),
				'active' => Http::getPost('active') ? 1 : 0,
			]);

			if (Http::getPost('password')) {
				$cols->add('password', password_hash(Http::getPost('password'), PASSWORD_DEFAULT));
			}

			if (Http::getPost('id')) {
				$this->db->query(
					"UPDATE e_admin_user SET %s WHERE id = %s",
					$cols,
					(int) Http::getPost('id')
				);
			}
			else {
				$this->db->query("INSERT INTO e_admin_user SET creation_date = NOW(), %s", $cols);
			}
			Http::redirect("?p=admin&action=adminUsers&m=1");
		}
	}

	public function output() {
		$this->getTemplate()->setFileName('admin/adminUserEdit.twig');

		if (Http::getGet('id')) {
			$this->getTemplate()->setValue(
				'item',
				$this->db->getRow("SELECT id, login, active FROM e_admin_user WHERE id = %s", Http::getGet('id'))
			);
		}

		return parent::output();
	}

}
