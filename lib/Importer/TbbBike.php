<?php

namespace PunchyRascal\DonkeyCms\Importer;

class TbbBike extends Base {

	public function getProducts() {
		$items = (array) $this->getXml()->children();
		return $items['SHOPITEM'];
	}

	public function getItemName($item) {
		return html_entity_decode($item->PRODUCT, ENT_QUOTES, 'utf-8');
	}

	public function getItemPrice($item) {
		return $item->PRICE_VAT;
	}

	public function getItemText($item) {
		return '';
	}

	public function getItemImgUrl($item) {
		return $item->IMGURL;
	}

	public function getItemId($item) {
		return $item->ITEM_ID;
	}

	public function getItemBrand($item) {
		$parts = explode(' ', trim($item->PRODUCT));
		return isset($parts[0]) ? $parts[0] : '';
	}

	public function getItemStock($item) {
		return $item->DELIVERY_DATE == 0 ? 1 : 0;
	}

}
