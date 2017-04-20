<?php

namespace PunchyRascal\DonkeyCms\Controller\Admin;

use PunchyRascal\DonkeyCms\Http;

class ProductImports extends Base {

	public function output() {
		$this->getTemplate()
			->setFileName('admin/productImports.twig')
			->setValue('items', $this->db->query("SELECT * FROM e_product_import_setup ORDER BY name"));

		return parent::output();
	}

}