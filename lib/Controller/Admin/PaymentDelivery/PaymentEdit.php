<?php

namespace PunchyRascal\DonkeyCms\Controller\Admin\PaymentDelivery;

use PunchyRascal\DonkeyCms\Http;

class PaymentEdit extends \PunchyRascal\DonkeyCms\Controller\Admin\Base {

	private $id;

	public function execute() {
		$this->id = Http::getGet('id', Http::getPost('id'));

		if (Http::getPost('paymentEditSubmit')) {
			if ($this->id) {
				$this->db->query(
					"UPDATE e_payment_method SET
						name = %s,
						priority = %s,
						type = %s,
						price = %s
					WHERE id = %s",
					Http::getPost('name'),
					Http::getPost('priority'),
					Http::getPost('type'),
					Http::getPost('price'),
					$this->id
				);
				Http::redirect('/?p=admin&action=paymentDelivery&m=1');
			} else {

			}
		}
	}

	public function output() {
		$payment = $this->db->getRow("SELECT * FROM e_payment_method WHERE id = %s", $this->id);
		if (!$payment) {
			Http::markResponseNotFound();
		}
		$this->getTemplate()
			->setFileName('admin/paymentDelivery/paymentEdit.twig')
			->setValues([
				'item' => $payment,
				'pageTitle' => $payment['name']
			]);

		return parent::output();
	}

}
