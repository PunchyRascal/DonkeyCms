<?php

namespace PunchyRascal\DonkeyCms\Controller\Admin;

use PunchyRascal\DonkeyCms\Http;
use PunchyRascal\DonkeyCms\Model\Order;
use PunchyRascal\DonkeyCms\Pager;

class Orders extends Base {

	private $searchQuery;

	private $rowLimit = 15;

	public function execute() {
		$this->searchQuery = Http::getGet('q');

		if (Http::getPost('orderStatusSubmit')) {
			$status = $this->db->getColumn("SELECT status FROM e_order WHERE id = %s", Http::getPost('id'));
			$this->db->query(
				"UPDATE e_order SET status = %s, status_note = %s WHERE id = %s",
				Http::getPost('order_status'),
				Http::getPost('order_status_note'),
				Http::getPost('id')
			);
			if ($status != Http::getPost('order_status')) {
				$this->db->query(
					"UPDATE e_order SET status_change_date = NOW() WHERE id = %s",
					Http::getPost('id')
				);
			}
			if (Http::getPost('backUrl')) {
				Http::redirect(Http::getPost('backUrl'));
			}
			Http::redirect("/?p=admin&action=e_orders&status=" . Http::getPost('currentStatus'));
		}
	}

	public function output() {
		$this->getTemplate()->setFileName('admin/orders.twig');
		$status = $this->getListStatus();
		$query = $this->db->createSelectQuery();
		$query
			->calcFoundRows()
			->select('*')
			->from('e_order')
			->limitFrom(Http::getGet('from', 0))
			->limitCount($this->rowLimit)
			->orderBy('date DESC');

		if ($status == -1 AND $this->searchQuery) {
			$dbQueryParam = $this->db->createParam('TEXT_FUZZY', trim($this->searchQuery));
			$query
				->where('id = %s', $this->searchQuery)
				->where('OR name LIKE %s', $dbQueryParam)
				->where('OR street LIKE %s', $dbQueryParam)
				->where('OR town LIKE %s', $dbQueryParam)
				->where('OR email LIKE %s', $dbQueryParam)
				->where('OR phone LIKE %s', $dbQueryParam)
				->where('OR note LIKE %s', $dbQueryParam)
				->where('OR status_note LIKE %s', $dbQueryParam);
			$orders = $this->db->query($query);
			$this->getTemplate()->setValue(
				'searchMessage',
				"Nalezeno ". $this->db->foundRows() ." záznamů"
			);
			$this->log->info(
				"Order search: '%s', found: %d",
				$this->searchQuery,
				$this->db->foundRows()
			);
		} elseif($status != -1) {
			$query->where('status = %s', $status);
			$orders = $this->db->query($query);
		} else {
			$orders = [];
		}

		$allCount = $this->db->foundRows();

		foreach ($orders AS &$order_row) {
			$order = Order::getInstace($this->db, $this->app, $order_row['id']);
			$order->setData($order_row);
			$order_row['model'] = $order;
		}

		$this->getTemplate()->appendArray(array(
			'counts' => array(
				'new' => $this->db->getColumn("SELECT COUNT(*) FROM e_order WHERE status = 1"),
				'waiting' => $this->db->getColumn("SELECT COUNT(*) FROM e_order WHERE status = 2"),
				'confirmed' => $this->db->getColumn("SELECT COUNT(*) FROM e_order WHERE status = 3"),
			),
			'orders' => $orders,
			'currentStatus' => $this->getListStatus(),
			'paging' => new Pager($this->rowLimit, $allCount, [
				'action' => 'e_orders',
				'status' => $this->getListStatus(),
				"q" => $this->searchQuery,
			]),
			'searchQuery' => $this->searchQuery
		));
		return parent::output();
	}

	private function getListStatus() {
		return (int) Http::getGet('status', 1);
	}

}
