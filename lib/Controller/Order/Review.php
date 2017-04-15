<?php

namespace PunchyRascal\DonkeyCms\Controller\Order;

use PunchyRascal\DonkeyCms\Http;
use PunchyRascal\DonkeyCms\Session;
use PunchyRascal\DonkeyCms\Format;
use PunchyRascal\DonkeyCms\Model\Order;

class Review extends \PunchyRascal\DonkeyCms\Controller\Base {

	/**
	 * @var \PunchyRascal\DonkeyCms\Model\Order
	 */
	private $order;

	public function execute() {
		$this->order = Order::getInstace($this->db, $this->app, Session::get('orderId'));

		if (!$this->order->payment OR !$this->order->delivery) {
			Http::redirect('/?p=kosik&m=21');
		}

		if (!Http::getPost('orderFinal')) {
			return;
		}

		if (
			!(
				$this->order->name
				AND $this->order->getItems()
				AND $this->order->street
				AND $this->order->zip
				AND $this->order->town
				AND $this->order->payment
				AND $this->order->delivery
				AND Format::checkEmail($this->order->email)
				AND $this->order->phone
			)
		) {
			Http::redirect('/?p=kosik&m=21');
		}

		$this->placeOrder();
	}

	public function output() {
		$this->getTemplate()
			->setFileName('order/review.twig')
			->setValue('model', $this->order);

		return parent::output();
	}

	private function placeOrder() {
		$this->order->place();
		Session::delete('orderId');
		Http::redirect("/?p=objednavka-odeslana&oid=" . Format::scramble($this->order->id));
	}

}
