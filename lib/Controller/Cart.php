<?php

namespace PunchyRascal\DonkeyCms\Controller;

use PunchyRascal\DonkeyCms\Session;
use PunchyRascal\DonkeyCms\Http;
use PunchyRascal\DonkeyCms\Model\Order;

class Cart extends Base {

	private $selectShowPrice;
	private $sum;
	/**
	 * @var Order
	 */
	private $order;

	public function execute() {
		if (!Session::get('orderId')) {
			$this->order = new Order($this->db, $this->app);
			Session::set('orderId', $this->order->id);
		} else {
			$this->order = Order::getInstace($this->db, $this->app, Session::get('orderId'));
		}

		if (Http::getPost('zasilkovna_save')) {
			$this->order->zasilkovna_branch = Http::getPost('pobocka') ?: null;
		}

		$this->handleCartItems();

		if (Http::getPost('delivery')) {
			$this->order->delivery = Http::getPost('delivery');
		}

		if (Http::getPost('payment')) {
			$this->order->payment = Http::getPost('payment');
		}

		$this->sum = $this->order->getItemsTotal();
		$this->selectShowPrice = $this->sum <= Order::POSTAGE_DIS_THRESHOLD;
	}

	private function handleCartItems() {
		/**
		 * Adding product
		 */
		if (Http::getPost('buy_submit') AND Http::getPost('id')) {
			$productId = Http::getPost('id');

			try {
				$this->order->addItemByProductId($productId, Http::getPost('variant'));
			} catch(\PunchyRascal\DonkeyCms\Model\Exception\ProductDoesNotExistException $e) {
				return;
			}

			Http::redirect("/?p=kosik");
		}

		/**
		 * Changing amount / removing
		 */
		if (Http::getPost('re_amount') !== null) {
			if (Http::getPost('re_amount') <= 0) {
				$this->order->removeItem(Http::getPost('item_id'));
				Http::redirect("/?p=kosik");
			}
			$this->order->setItemAmount(Http::getPost('item_id'), Http::getPost('re_amount'));
			Http::redirect("/?p=kosik");
		}
	}

	public function output() {
		$this->getTemplate()->setFileName('cart.twig');
		$this->getTemplate()->data = [
			'selectShowPrice' => $this->selectShowPrice,
			'items' => $this->order->getItems(),
			'pageTitle' => 'Košík',
			'sum' => $this->sum,
			'remainsToSpend' => Order::POSTAGE_DIS_THRESHOLD - $this->sum + 1,
			'delivery' => $this->order->delivery,
			'payment' => $this->order->payment,
			'showZasilkovna' => $this->sum <= Order::ZASILKOVNA_MAX_VALUE,
			'postageThreshold' => Order::POSTAGE_DIS_THRESHOLD,
			'paymentPrice' => $this->order->getPaymentPrice(),
			'postage' => $this->order->getPostagePrice(),
			'paymentMethods' => $this->db->query('SELECT * FROM e_payment_method ORDER BY priority'),
			'deliveryMethods' => $this->order->getAvailableDeliveryMethods(),
			'zasilkovnaSet' => (bool) $this->order->zasilkovna_branch
		];
		return parent::output();
	}

}
