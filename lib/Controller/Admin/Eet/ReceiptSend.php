<?php

namespace PunchyRascal\DonkeyCms\Controller\Admin\Eet;

use PunchyRascal\DonkeyCms\Http;

use FilipSedivy\EET\Certificate;
use PunchyRascal\DonkeyCms\Model\EetDispatcher;
use FilipSedivy\EET\Receipt;
use FilipSedivy\EET\Utils\UUID;


class ReceiptSend extends \PunchyRascal\DonkeyCms\Controller\Admin\Base {

	/**
	 * Bezpečnostní kód poplatníka
	 * @var string
	 */
	private $bkp;

	/**
	 * Podpisový kód poplatníka
	 * @var string
	 */
	private $pkp;

	public function execute() {

		if (Http::getPost('sendSale')) {

			if (Http::getPost("id")) {
				$this->handleReSend(Http::getPost('id'));
			} else {
				if (!is_numeric(Http::getPost('amount'))) {
					Http::redirect("?p=admin&action=eetSend&m=12");
				}
				$this->handleSend(
					Http::getPost('amount'),
					(bool) Http::getPost('verification_only', false)
				);
			}

		}

	}

	public function output() {
		$this->getTemplate()->setFileName('admin/eet/send.twig');
		return parent::output();
	}

	private function handleSend($amount, $verificationOnly) {
		$this->db->query(
			"INSERT INTO e_eet_sale SET created_at = NOW(), amount = %s,
				verification_only = %s",
			$amount, $verificationOnly ? 1 : 0
		);
		$id = $this->db->getInsertId();
		try {
			$fik = $this->sendReceipt($id, $amount, $verificationOnly);
			$this->db->query(
				"UPDATE e_eet_sale
				SET fik = %s, bkp = %s, pkp = %s, sent_ok = 1
				WHERE id = %s",
				$fik, $this->bkp, $this->pkp, $id
			);
			if ($fik) {
				Http::redirect("/?p=admin&action=eetList&m=1");
			} else {
				Http::redirect("/?p=admin&action=eetList&m=23");
			}
		} catch(\Exception $e) {
			$log = new \PunchyRascal\DonkeyCms\Logger('eet-receipt.log');
			$log->error("Receipt ID: $id - $e");
			Http::redirect("/?p=admin&action=eetList&m=23");
		}
	}

	private function handleReSend($id) {
		$receipt = $this->db->getRow("SELECT * FROM e_eet_sale WHERE id = %s", $id);
		try {
			$fik = $this->sendReceipt($receipt['id'], $receipt['amount'], $receipt['verification_only']);
			$this->db->query(
				"UPDATE e_eet_sale
				SET fik = %s, sent_ok = 1
				WHERE id = %s",
				$fik, $receipt['id']
			);
			if ($fik) {
				Http::redirect("/?p=admin&action=eetList&m=1");
			} else {
				Http::redirect("/?p=admin&action=eetList&m=23");
			}
		} catch(\Exception $e) {
			$log = new \PunchyRascal\DonkeyCms\Logger('eet-receipt.log');
			$log->error("Receipt ID: $receipt[id] - $e");
			Http::redirect("/?p=admin&action=eetList&m=23");
		}
	}

	private function sendReceipt($receiptId, $amount, $verificationOnly) {
		$dispatcher = $this->getDispatcher();
		$receipt = $this->getReceipt($receiptId, $amount);
		$result = $dispatcher->send($receipt, $verificationOnly);

		$this->bkp = $dispatcher->bkp;
		$this->pkp = $dispatcher->pkp;

		if ($verificationOnly AND $result !== true) {
			throw new \Exception("Verifying sale returned FIK - $result");
		}

		return $verificationOnly ? null : $result;
	}

	private function getReceipt($receiptId, $amount) {
		$receipt = new Receipt();
		$receipt->uuid_zpravy = UUID::v4();
		$receipt->dic_popl = 'CZ683555118';		# CZ8206032032
		$receipt->id_provoz = '11';				# TODO
		$receipt->id_pokl = '1';				# TODO
		$receipt->porad_cis = $receiptId;
		$receipt->dat_trzby = new \DateTime;
		$receipt->celk_trzba = $amount;
		return $receipt;
	}

	private function getDispatcher() {
		$certificate = new Certificate(
			__DIR__ . '/../../../../../../../eet/EET_CA1_Playground-CZ683555118.p12',
			'eet'
		);
		return new EetDispatcher(
			__DIR__ . '/../../../../../../../eet/wsdl.xml',
			$certificate
		);
	}

}
