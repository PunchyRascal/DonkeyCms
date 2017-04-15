<?php

namespace PunchyRascal\DonkeyCms\Importer;

class Snowbitch extends Base {

	public function getItemBrand($item) {
		return $item->MANUFACTURER;
	}

	public function getItemId($item) {
		return $item->CODE;
	}

	public function getItemImgUrl($item) {
		return $item->IMGURL;
	}

	public function getItemName($item) {
		return $item->PRODUCT;
	}

	public function getItemPrice($item) {
		return $item->PRICE_VAT;
	}

	public function getItemStock($item) {
		return 1;
	}

	public function getAvailabilityDescription($item) {
		$days = $item->AVAILABILITY + 1;
		if ($days == 1) {
			$word = "den";
		} elseif($days > 1 AND $days < 5) {
			$word = 'dny';
		} else {
			$word = 'dní';
		}
		return "Externí sklad, $days $word";
	}

	public function getAvailabilityInDays($item) {
		return $item->AVAILABILITY + 1;
	}

	public function getItemText($item) {
		return $item->DESCRIPTION;
	}

	public function getProducts() {
		$items = (array) $this->getXml()->children();
		return $items['SHOPITEM'];
	}

}