<?php

namespace PunchyRascal\DonkeyCms\Importer;


class Dema extends Base {

	public function getItemBrand($item) {
		return $item->MANUFACTURER;
	}

	public function getItemId($item) {
		return $item->PRODUCT_NO;
	}

	public function getItemImgUrl($item) {
		if ($item->IMAGES AND $item->IMAGES->IMGURL) {
			if (is_array($item->IMAGES->IMGURL)) {
				return $item->IMAGES->IMGURL[0];
			}
			return $item->IMAGES->IMGURL;
		}
	}

	public function getItemName($item) {
		return $item->PRODUCTNAME;
	}

	public function getItemPrice($item) {
		return round($item->PRICE_VAT * 0.8);
	}

	public function getItemStock($item) {
		return $item->DELIVERY_DATE == 0 ? 1 : 0;
	}

	public function getItemText($item) {
		return $item->DESCRIPTION;
	}

	public function getProducts() {
		$feed = (array) $this->getXml()->children();
		return $feed['SHOPITEM'];
	}
}
