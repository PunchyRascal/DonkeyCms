<?php

namespace PunchyRascal\DonkeyCms\Importer;

class Katmar extends Base {

	public function getProducts() {
		$items = (array) $this->getXml()->children();
		return $items['SHOPITEM'];
	}

	public function getItemName($item) {
		return $item->PRODUCT;
	}

	public function getItemPrice($item) {
		return $item->PRICE_VAT;
	}

	public function getItemText($item) {
		return $item->DESCRIPTION;
	}

	public function getItemImgUrl($item) {
		return $item->IMGURL;
	}

	public function getItemId($item) {
		return $item->PRODUCTNO;
	}

	public function getItemBrand($item) {
		return $item->MANUFACTURER;
	}

	public function getItemStock($item) {
		return 1;
	}

}
