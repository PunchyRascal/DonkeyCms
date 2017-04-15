<?php

namespace PunchyRascal\DonkeyCms\Controller\Admin;

use PunchyRascal\DonkeyCms\Http;
use PunchyRascal\DonkeyCms\Model\Order;
use PunchyRascal\DonkeyCms\Model\Product;

class OrderDetail extends Base {

	private $id;

	/**
	 * @var Order
	 */
	private $model;

	public function execute() {
		parent::execute();
		$this->id = (int) Http::getGet('id', Http::getPost('id'));
		$this->model = Order::getInstace(
			$this->db,
			$this->app,
			$this->id
		);

		if (Http::getPost('removeRow')) {
			$this->handleRowRemoval();
		}

		if (Http::getPost('addRow')) {
			$this->handleRowAdd();
		}
	}

	public function output() {
		$this->getTemplate()
			->setFileName('admin/orderDetail.twig')
			->setValue('pageTitle', 'Objednávka č. ' . $this->id)
			->setValue('model', $this->model);
		return parent::output();
	}

	private function handleRowRemoval() {
		$this->model->removeItem(Http::getPost('itemId'));
		Http::redirect('/?p=admin&action=orderDetail&id=' . $this->id . '&m=1');
	}

	private function handleRowAdd() {
		try {
			$product = Product::getInstace($this->app, Http::getPost('productId'));
			$variants = $product->getVariants();
			if ($variants AND !Http::getPost('addVariant')) {
				$this->getTemplate()->setValue('addVariants', $variants)
					->setValue('addProductId', $product->id);
				return;
			}
			$this->model->addItemByProductId($product->id, Http::getPost('addVariant'));
		} catch (\PunchyRascal\DonkeyCms\Model\Exception\ProductDoesNotExistException $e) {
			Http::redirect('/?p=admin&action=orderDetail&id=' . $this->id . '&m=22');
		}
		Http::redirect('/?p=admin&action=orderDetail&id=' . $this->id . '&m=1');
	}

}