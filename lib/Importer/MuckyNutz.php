<?php

namespace PunchyRascal\DonkeyCms\Importer;

class MuckyNutz extends Base {

	public function getItemBrand($item) {
		return $item->MANUFACTURER;
	}

	public function getItemId($item) {
		return $item->PRODUCTNO;
	}

	public function getItemImgUrl($item) {
		return $item->IMGURL;
	}

	public function getItemName($item) {
		return $item->PRODUCTNAME;
	}

	public function getItemPrice($item) {
		return $item->PRICE_VAT;
	}

	public function getItemStock($item) {
		return $item->DELIVERY_DATE == 0 ? 1 : 0;
	}

	public function getItemText($item) {
		return $item->DESCRIPTION;
	}

	public function getProducts() {
		$items = (array) $this->getXml()->children();
		return $items['SHOPITEM'];
	}

}