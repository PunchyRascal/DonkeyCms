<?php

namespace PunchyRascal\DonkeyCms\Controller\Admin;

use PunchyRascal\DonkeyCms\Http;
use PunchyRascal\DonkeyCms\Model\Order;

class Invoice extends Base {

	private $id;

	public function execute() {
		$this->id = Http::getGet('id');
	}

	public function output() {
		if (!$this->id) {
			return "Missing invoice ID parameter";
		}

		$this->getTemplate()->setFileName('admin/invoice.twig');
		$model = Order::getInstace($this->db, $this->app, $this->id);
		$this->getTemplate()->setValue('model', $model);

		return parent::output();
	}

}
