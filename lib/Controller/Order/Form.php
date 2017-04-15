<?php

namespace PunchyRascal\DonkeyCms\Controller\Order;

use PunchyRascal\DonkeyCms\Http;
use PunchyRascal\DonkeyCms\Session;
use PunchyRascal\DonkeyCms\Format;
use PunchyRascal\DonkeyCms\Model\Order;

class Form extends \PunchyRascal\DonkeyCms\Controller\Base {

	/**
	 * @var \PunchyRascal\DonkeyCms\Model\Order
	 */
	private $order;

	public function execute() {
		$this->order = Order::getInstace($this->db, $this->app, Session::get('orderId'));

		if (Http::getPost('order_submit1') AND $this->isFormDataValid()) {
			$this->order->name = Http::getPost('name');
			$this->order->street = Http::getPost('street');
			$this->order->town = Http::getPost('town');
			$this->order->zip = Http::getPost('zip');
			$this->order->email = Http::getPost('email');
			$this->order->phone = Http::getPost('phone');
			$this->order->note = Http::getPost('note');
			Http::redirect('/?p=objednavka-rekapitulace');
		}

		if (Http::getPost('origin') === 'startOrder'
				AND $this->order->delivery == 'zasilkovna'
				AND !$this->order->zasilkovna_branch) {
			Http::redirect('/?p=kosik&m=20');
		}

		if (!$this->order->payment OR !$this->order->delivery) {
			Http::redirect('/?p=kosik&m=21');
		}
	}

	public function output() {
		$this->getTemplate()->setFileName('order/form.twig')->setValues([
			'order' => $this->order,
			'showFormError' => (
				Http::getPost('order_submit1')
				AND !$this->isFormDataValid()
			),
		]);
		return parent::output();
	}

	private function isFormDataValid() {
		return (
			Http::getPost('name')
			AND Http::getPost('zip')
			AND Http::getPost('street')
			AND Http::getPost('town')
			AND Format::checkEmail(Http::getPost('email'))
			AND Http::getPost('phone')
		);
	}

}
