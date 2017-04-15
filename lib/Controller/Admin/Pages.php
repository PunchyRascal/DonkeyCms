<?php

namespace PunchyRascal\DonkeyCms\Controller\Admin;

class Pages extends Base {

	public function output() {
		$this->getTemplate()->setFileName('admin/pages.twig');

		$items = $this->db->query("SELECT name, url, modified, id, active FROM e_page ORDER BY name");

		$this->getTemplate()->setValue('items', $items);

		return parent::output();
	}

}
