<?php

namespace PunchyRascal\DonkeyCms\Importer;

class Cykloprofi extends Base {

	public function getItemBrand($item) {
		return $item->MANUFACTURED;
	}

	public function getItemId($item) {
		return $item->CATALOGUENUMBER;
	}

	public function getItemImgUrl($item) {
		return $item->IMGURL;
	}

	public function getItemName($item) {
		return $item->PRODUCTNAME;
	}

	public function getItemPrice($item) {
		return $item->PRICEMOC_VAT;
	}

	public function getItemStock($item) {
		return $item->STOCK;
	}

	public function getItemText($item) {
		return $item->DESCRIPTION . $item->ENHANCEDDESCRIPTION;
	}

	public function getProducts() {
		$items = (array) $this->getXml()->children();
		return $items['SHOPITEM'];
	}

}
