<?php

namespace PunchyRascal\DonkeyCms\Importer;

class Cyklozitny extends Base {

	public function getProducts() {
		$items = (array) $this->getXml()->children();
		$items = (array) $items['items'][1]->children();
		return $items['item'];
	}

	public function getItemName($item) {
		return $item->name;
	}

	public function getItemPrice($item) {
		return $item->price_moc * 0.95;
	}

	public function getItemText($item) {
		return $item->description_full;
	}

	public function getItemImgUrl($item) {
		return $item->images->url;
	}

	public function getItemId($item) {
		return $item->id;
	}

	public function getItemBrand($item) {
		return $item->manufacturer;
	}

	public function getItemStock($item) {
		return $item->store_count;
	}

	public function getStocks() {
		$items = (array) $this->getStockXml()->children();
		return $items['items']['item'];
	}

	public function getItemSpecialId($item) {
		return $item->id;
	}

	public function getItemSpecialStock($item) {
		if ($item->delivery_date >= 0 AND $item->delivery_date < 3) {
			return 1;
		}
		return 0;
	}

}
