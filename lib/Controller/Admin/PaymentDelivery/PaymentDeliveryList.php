<?php

namespace PunchyRascal\DonkeyCms\Controller\Admin\PaymentDelivery;

class PaymentDeliveryList extends \PunchyRascal\DonkeyCms\Controller\Admin\Base {

	public function output() {
		$this->getTemplate()
			->setFileName('admin/paymentDelivery/list.twig')
			->setValues([
				'payments' =>  $this->db->query("SELECT * FROM e_payment_method ORDER BY priority"),
				'deliveries' => $this->db->query("SELECT * FROM e_delivery_method ORDER BY priority"),
				'pageTitle' => 'Ceny dopravy a platby',
			]);

		return parent::output();
	}

}
