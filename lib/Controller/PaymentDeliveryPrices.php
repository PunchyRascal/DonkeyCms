<?php

namespace PunchyRascal\DonkeyCms\Controller;

class PaymentDeliveryPrices extends Base {

	public function output() {
		$this->getTemplate()
			->setFileName('paymentDeliveryPrices.twig')
			->setValues([
				'payments' =>  $this->db->query("SELECT * FROM e_payment_method ORDER BY priority"),
				'deliveries' => $this->db->query("SELECT * FROM e_delivery_method ORDER BY priority"),
				'freeThreshold' => \PunchyRascal\DonkeyCms\Model\Order::POSTAGE_DIS_THRESHOLD,
				'pageTitle' => 'Ceny dopravy a platby',
			]);

		return parent::output();
	}

}
