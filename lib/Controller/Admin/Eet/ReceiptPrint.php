<?php

namespace PunchyRascal\DonkeyCms\Controller\Admin\Eet;

use PunchyRascal\DonkeyCms\Http;

class ReceiptPrint extends \PunchyRascal\DonkeyCms\Controller\Admin\Base {

	public function execute() {
		if (Http::getPost('addRow')) {
			$this->db->query(
				"INSERT INTO e_eet_sale_item SET sale_id = %s, amount = %s,
					description = %s, price = %s",
				Http::getPost('receiptId'),
				Http::getPost('amount'),
				Http::getPost('description'),
				Http::getPost('price')
			);
			Http::redirect('/?p=admin&action=eetReceiptPrint&id='. Http::getPost('receiptId'));
		}

		if (Http::getPost('removeItem')) {
			$this->db->query(
				"DELETE FROM e_eet_sale_item WHERE id = %s LIMIT 1",
				Http::getPost('id')
			);
			Http::redirect('/?p=admin&action=eetReceiptPrint&id='. Http::getPost('receiptId'));
		}
	}

	public function output() {
		$this->getTemplate()
			->setFileName('admin/eet/receiptPrint.twig')
			->setValue(
				'receipt',
				$this->db->getRow(
					"SELECT sale.*, SUM(item.amount * item.price) AS itemsSum
						FROM e_eet_sale AS sale
						LEFT JOIN e_eet_sale_item AS item ON item.sale_id = sale.id
						WHERE sale.id = %d",
					(int) Http::getGet('id')
				)
			)
			->setValue(
				'items',
				$this->db->query(
					"SELECT * FROM e_eet_sale_item WHERE sale_id = %d",
					(int) Http::getGet('id')
				)
			);

		return parent::output();
	}

}
