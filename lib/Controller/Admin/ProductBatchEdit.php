<?php

namespace PunchyRascal\DonkeyCms\Controller\Admin;

use PunchyRascal\DonkeyCms\Http;

class ProductBatchEdit extends Base {

	public function execute() {
		foreach (Http::getPost('ids') AS $id) {
			$this->db->query(
				"UPDATE e_item SET
					price = %s,
					stock = %s,
					availability = %s,
					availability_days = %s,
					discount = %s,
					auto_update = %s
					WHERE id = %s",
				(int) Http::getPost("price|$id"),
				(int) Http::getPost("stock|$id"),
				Http::getPost("availability|$id"),
				(int) Http::getPost("availability_days|$id"),
				(int) Http::getPost("discount|$id"),
				(int) Http::getPost("auto_update|$id"),
				(int) $id
			);
		}
		$make = urlencode(Http::getPost('make'));
		$cat = urlencode(Http::getPost('cat'));
		$eSearch = urlencode(Http::getPost('eSearch'));
		$filter = urlencode(Http::getPost('filter'));
		$from = urlencode(Http::getPost('from'));
		Http::redirect("/?p=admin&action=e_items&make=$make&cat=$cat&eSearch=$eSearch&filter=$filter&from=$from");
	}

}
