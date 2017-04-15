<?php

namespace PunchyRascal\DonkeyCms\Controller\Admin;

class OrderResume extends Base {

	public function output() {
		$this->getTemplate()
			->setFileName('admin/orderResume.twig')
			->setValue('pageTitle', 'Statistiky objednávek')
			->setValues($this->getSentPerYearData())
			->setValues($this->getSentPerMonthData())
			->setValues($this->getSentPerMonthPrevData())
			->setValues($this->getCancelledPerMonthData())
			->setValues($this->getCancelledPerMonthPrevData());
		return parent::output();
	}

	private function getSentPerYearData() {
		$sent = $this->db->query("
			SELECT
				SUM(price_items) AS amount,
				YEAR(date) AS label
			FROM e_order
			WHERE status = 4
			GROUP BY YEAR(date)
			ORDER BY date
		");
		$labels = [];
		$data = [];
		$estimate = [];
		$row = [];
		foreach ($sent AS $row) {
			array_push($data, (int) $row['amount']);
			array_push($labels, $row['label']);
			array_push($estimate, null);
		}
		array_pop($estimate);
		$dayIndex = date('z') + 1; # 1 - 366
		array_push($estimate, round($row['amount'] / $dayIndex * 365));

		return [
			'ordersSentYearsLabels' => $labels,
			'ordersSentYearsData' => $data,
			'ordersSentYearsProjectionData' => $estimate
		];
	}

	private function getSentPerMonthData() {
		$sent = $this->db->query("
			SELECT
				SUM(price_items) AS amount,
				CONCAT(MONTH(date_record), '/', YEAR(date_record)) AS label
			FROM e_dates AS dates
			LEFT JOIN e_order AS ord
				ON CONCAT(MONTH(ord.date), '/', YEAR(ord.date)) = CONCAT(MONTH(date_record), '/', YEAR(date_record))
					AND ord.status = 4
			WHERE
				date_record > DATE_FORMAT(DATE_SUB(NOW(), INTERVAL 12 MONTH) ,'%Y-%m-01')
				AND date_record <= DATE_FORMAT(NOW() ,'%Y-%m-01')
			GROUP BY CONCAT(MONTH(date_record), '/', YEAR(date_record))
			ORDER BY date_record
		");
		$labels = [];
		$data = [];
		foreach ($sent AS $row) {
			array_push($data, (int) $row['amount']);
			array_push($labels, $row['label']);
		}
		return [
			'ordersSentLabels' => $labels,
			'ordersSentData' => $data
		];
	}

	private function getSentPerMonthPrevData() {
		$sent = $this->db->query("
			SELECT
				SUM(price_items) AS amount,
				CONCAT(MONTH(date_record), '/', YEAR(date_record)) AS label
			FROM e_dates AS dates
			LEFT JOIN e_order AS ord
				ON CONCAT(MONTH(ord.date), '/', YEAR(ord.date)) = CONCAT(MONTH(date_record), '/', YEAR(date_record))
					AND ord.status = 4
			WHERE
				date_record > DATE_FORMAT(DATE_SUB(NOW(), INTERVAL 24 MONTH), '%Y-%m-01')
				AND date_record <= DATE_FORMAT(DATE_SUB(NOW(), INTERVAL 12 MONTH), '%Y-%m-01')
			GROUP BY CONCAT(MONTH(date_record), '/', YEAR(date_record))
			ORDER BY date_record
		");
		$data = [];
		$labels = [];
		foreach ($sent AS $row) {
			array_push($data, (int) $row['amount']);
			array_push($labels, $row['label']);
		}
		return [
			'ordersSentPrevLabels' => $labels,
			'ordersSentPrevData' => $data,
		];
	}

	private function getCancelledPerMonthData() {
		$sent = $this->db->query("
			SELECT
				SUM(price_items) AS amount,
				CONCAT(MONTH(date_record), '/', YEAR(date_record)) AS label
			FROM e_dates AS dates
			LEFT JOIN e_order AS ord
				ON CONCAT(MONTH(ord.date), '/', YEAR(ord.date)) = CONCAT(MONTH(date_record), '/', YEAR(date_record))
					AND ord.status IN(5, 6)
			WHERE
				date_record > DATE_FORMAT(DATE_SUB(NOW(), INTERVAL 12 MONTH), '%Y-%m-01')
				AND date_record <= DATE_FORMAT(NOW(), '%Y-%m-01')
			GROUP BY CONCAT(MONTH(date_record), '/', YEAR(date_record))
			ORDER BY date_record
		");
		$labels = [];
		$data = [];
		foreach ($sent AS $row) {
			array_push($data, (int) $row['amount']);
			array_push($labels, $row['label']);
		}
		return [
			'ordersCancelledLabels' => $labels,
			'ordersCancelledData' => $data
		];
	}

	private function getCancelledPerMonthPrevData() {
		$sent = $this->db->query("
			SELECT
				SUM(price_items) AS amount
			FROM e_dates AS dates
			LEFT JOIN e_order AS ord
				ON CONCAT(MONTH(ord.date), '/', YEAR(ord.date)) = CONCAT(MONTH(date_record), '/', YEAR(date_record))
					AND status IN(5, 6)
			WHERE
				date_record > DATE_FORMAT(DATE_SUB(NOW(), INTERVAL 24 MONTH), '%Y-%m-01')
				AND date_record <= DATE_FORMAT(DATE_SUB(NOW(), INTERVAL 12 MONTH), '%Y-%m-01')
			GROUP BY CONCAT(MONTH(date_record), '/', YEAR(date_record))
			ORDER BY date_record
		");
		$data = [];
		foreach ($sent AS $row) {
			array_push($data, (int) $row['amount']);
		}
		return [
			'ordersCancelledPrevData' => $data,
		];
	}

}
