<?php

namespace PunchyRascal\DonkeyCms\Controller\Order;

use PunchyRascal\DonkeyCms\Http;
use PunchyRascal\DonkeyCms\Format;
use PunchyRascal\DonkeyCms\Model;

class Sent extends \PunchyRascal\DonkeyCms\Controller\Base {

	public function output() {
		$this->getTemplate()
			->setFileName('order/done.twig')
			->setValue('useProductionFeatures', $this->app->useProductionFeatures());

		$id = Format::unScramble(Http::getGet('oid'));
		if ($id) {
			$order = Model\Order::getInstace($this->db, $this->app, $id);
			$this->getTemplate()
				->setValue('zboziChecksum', $this->config->zboziChecksum)
				->setValue('orderPrice', $order->getGrandTotal())
				->setValue('orderId', $order->public_id);
		}

		return parent::output();
	}

}
