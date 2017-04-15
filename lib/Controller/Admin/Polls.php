<?php

namespace PunchyRascal\DonkeyCms\Controller\Admin;

use PunchyRascal\DonkeyCms\Http;

class Polls extends Base {

	public function execute() {
		if (Http::getGet('del_ank') AND is_numeric(Http::getGet('id'))) {
			$this->db->query("DELETE FROM e_poll WHERE id = %s", Http::getGet('id'));
			Http::redirect("/?p=admin&action=polls&m=1");
		}

		if (Http::getGet('set_ank_active') AND is_numeric(Http::getGet('id'))) {
			$this->db->query("UPDATE e_poll SET active = %s WHERE id = %s",
				Http::getGet('set_ank_active') === 'off' ? '' : 'on',
				Http::getGet('id')
			);
			Http::redirect("/?p=admin&action=polls&m=1");
		}
	}

	public function output() {
		$this->getTemplate()->setFileName('admin/polls.twig');

		$this->getTemplate()->setValue('items', $this->db->query("SELECT * FROM e_poll ORDER BY date DESC"));

		return parent::output();
	}

}
