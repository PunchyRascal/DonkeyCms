<?php

namespace PunchyRascal\DonkeyCms\Importer;

class BpLumen extends Base {

	public function getProducts() {
		$items = (array) $this->getXml()->children();
		return $items['SHOPITEM'];
	}

	public function getItemName($item) {
		return $item->PRODUCT_NAME;
	}

	public function getItemPrice($item) {
		return $item->PRICE_MOC;
	}

	public function getItemText($item) {
		return $item->DESCRIPTION;
	}

	public function getItemImgUrl($item) {
		return $item->IMG_URL;
	}

	public function getItemId($item) {
		return $item->PRODUCT_ID;
	}

	public function getItemBrand($item) {
		return $item->ZNACKA;
	}

	public function getItemStock($item) {
		return $item->STORAGE;
	}

}
