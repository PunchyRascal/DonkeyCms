<?php

namespace PunchyRascal\DonkeyCms\Controller\Admin;

use PunchyRascal\DonkeyCms\Http;

/**
 * AdminUsers
 */
class AdminUsers extends Base {

	public function execute() {
		if (Http::getPost('deleteId')) {
			$this->db->query("DELETE FROM e_admin_user WHERE id = %s", Http::getPost('deleteId'));
			Http::redirect("/?p=admin&action=adminUsers&m=1");
		}
	}

	public function output() {
		$this->getTemplate()
			->setFileName('admin/adminUsers.twig')
			->setValue('items', $this->db->query(
				"SELECT id, active, login, last_login_date, creation_date
					FROM e_admin_user ORDER BY login")
			);


		return parent::output();
	}

}