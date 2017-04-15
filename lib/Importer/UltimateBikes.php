<?php

namespace PunchyRascal\DonkeyCms\Importer;

class UltimateBikes extends Base {

	public function getProducts() {
		$items = (array) $this->getXml()->children();
		return $items['products'];
	}

	public function getItemName($item) {
		return $item->name;
	}

	public function getItemPrice($item) {
		return round($item->price_mo + ($item->price_mo / 100 * \PunchyRascal\DonkeyCms\Application::VAT_RATE));
	}

	public function getItemText($item) {
		return $item->description2;
	}

	public function getItemImgUrl($item) {
		if ($item->imgs AND $item->imgs->img) {
			if (is_array($item->imgs->img)) {
				return $item->imgs->img[0];
			}
			return $item->imgs->img;
		}
	}

	public function getItemId($item) {
		return $item->code;
	}

	public function getItemBrand($item) {
		return $item->manufacturer;
	}

	public function getItemStock($item) {
		return $item->avaibility_q;
	}

}
