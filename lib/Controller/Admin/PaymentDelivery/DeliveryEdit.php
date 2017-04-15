<?php

namespace PunchyRascal\DonkeyCms\Controller\Admin\PaymentDelivery;

use PunchyRascal\DonkeyCms\Http;

class DeliveryEdit extends \PunchyRascal\DonkeyCms\Controller\Admin\Base {

	private $id;

	public function execute() {
		$this->id = Http::getGet('id', Http::getPost('id'));

		if (Http::getPost('deliveryEditSubmit')) {
			if ($this->id) {
				$this->db->query(
					"UPDATE e_delivery_method SET
						name = %s,
						priority = %s,
						price = %s
					WHERE id = %s",
					Http::getPost('name'),
					Http::getPost('priority'),
					Http::getPost('price'),
					$this->id
				);
				Http::redirect('/?p=admin&action=paymentDelivery&m=1');
			} else {

			}
		}
	}

	public function output() {
		$item = $this->db->getRow("SELECT * FROM e_delivery_method WHERE id = %s", $this->id);
		if (!$item) {
			Http::markResponseNotFound();
		}
		$this->getTemplate()
			->setFileName('admin/paymentDelivery/deliveryEdit.twig')
			->setValues([
				'item' => $item,
				'pageTitle' => $item['name']
			]);

		return parent::output();
	}

}
