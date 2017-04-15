<?php

namespace PunchyRascal\DonkeyCms\Importer;

class Bikestrike extends Base {

	public function getProducts() {
		$items = (array) $this->getXml()->children();
		return $items['SHOPITEM'];
	}

	public function getStocks() {
		$items = (array) $this->getStockXml()->children();
		return $items['item'];
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
		return $item->ITEM_ID;
	}

	public function getItemBrand($item) {
		return $item->MANUFACTURER;
	}

	public function getItemStock($item) {
		return null;
	}

	public function getItemSpecialStock($item) {
		return $item->stock_quantity;
	}

	public function getItemSpecialId($item) {
		return $item['id'];
	}

}
