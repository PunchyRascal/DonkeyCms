<?php

namespace PunchyRascal\DonkeyCms\Importer;

class Zbozi extends Base {

	public function getProducts() {
		$items = (array) $this->getXml()->children();
		return $items['SHOPITEM'];
	}

	public function getItemName($item) {
		return $item['PRODUCT'];
	}

	public function getItemPrice($item) {
		if ($item['PRICE_VAT']) {
			return $item['PRICE_VAT'];
		}
		if ($item['PRICE']) {
			return $item['PRICE'];
		}
		if ($item['PRICE_MOC']) {
			return $item['PRICE_MOC'];
		}
	}

	public function getItemText($item) {
		return $item['DESCRIPTION'];
	}

	public function getItemImgUrl($item) {
		return $item['IMGURL'];
	}

	public function getItemId($item) {
		return $item['PRODUCT_ID'];
	}

	public function getItemBrand($item) {
		return $item['MANUFACTURER'];
	}

	public function getItemStock($item) {
		return $item['STORAGE'];
	}

}
