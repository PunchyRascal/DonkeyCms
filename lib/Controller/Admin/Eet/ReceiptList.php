<?php

namespace PunchyRascal\DonkeyCms\Controller\Admin\Eet;

use PunchyRascal\DonkeyCms\Http;
use PunchyRascal\DonkeyCms\Pager;

class ReceiptList extends \PunchyRascal\DonkeyCms\Controller\Admin\Base {

	public function output() {
		$this->getTemplate()
			->setFileName('admin/eet/list.twig')
			->setValue(
				'items',
				$this->db->query(
					"SELECT SQL_CALC_FOUND_ROWS *,
						TIMEDIFF(DATE_ADD(created_at, INTERVAL 48 HOUR), NOW()) AS hours_left_to_resend,
						CASE WHEN DATE_ADD(created_at, INTERVAL 48 HOUR) < NOW()
							THEN 1 ELSE 0 END AS warn_time_is_up
						FROM e_eet_sale
						ORDER BY id DESC LIMIT %d, 15",
					(int) Http::getGet('from')
				)
			)
			->setValue('paging', new Pager(15, $this->db->foundRows(), ['action' => 'eetList']));

		return parent::output();
	}

}
