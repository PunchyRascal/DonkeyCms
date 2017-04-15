<?php

namespace PunchyRascal\DonkeyCms\Controller;

use PunchyRascal\DonkeyCms\Cache;
use PunchyRascal\DonkeyCms\Http;

class ProductExport extends Base {

	public function output() {
		header("Content-type: application/xml; charset=utf-8");

		$this->getTemplate()
			->setFileName('productExport.twig')
			->setValue('items', $this->getData())
			->setValue('showDelivery', Http::getGet('type') === 'heureka');

		return parent::output();
	}

	private function getData() {
		return $this->cache->getByParams(
			'productExport',
			[],
			function () {
				$data = $this->db->query("SELECT e_item.*, e_cat.name AS catName,
						item_image.extension, `parent`.`name` AS parentName, parent2.name AS parent2Name,
						delivery_post.price AS deliveryPriceCzPost,
						delivery_post.price + payment_cod.price AS deliveryPriceCzPostCod,
						delivery_ppl.price AS deliveryPrivePpl,
						delivery_ppl.price + payment_cod.price AS deliveryPricePplCod
					FROM e_item
					LEFT JOIN item_image ON e_item.id = item_image.home_art
					INNER JOIN e_cat ON e_item.home = e_cat.id AND e_cat.active = 1
					LEFT JOIN e_cat AS parent ON parent.id = e_cat.parent_cat
					LEFT JOIN e_cat AS parent2 ON parent2.id = parent.parent_cat
					INNER JOIN e_delivery_method AS delivery_post ON delivery_post.id = 'cz_post'
					INNER JOIN e_delivery_method AS delivery_ppl ON delivery_ppl.id = 'cz_ppl'
					INNER JOIN e_payment_method AS payment_cod ON payment_cod.id = 'cod'
					WHERE stock > 0 AND e_item.active = 1 AND e_item.price > 0
					GROUP BY e_item.id
					ORDER BY name
					"
				);
				return serialize($data);
			},
			3600 * 3,
			function ($data) {
				return unserialize($data);
			}
		);
	}

}
