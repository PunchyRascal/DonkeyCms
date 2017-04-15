<?php

namespace PunchyRascal\DonkeyCms\Model;

class EetDispatcher extends \FilipSedivy\EET\Dispatcher {

	/**
	 * Bezpečnostní kód poplatníka
	 * @var string
	 */
	public $bkp;

	/**
	 * Podpisový kód poplatníka
	 * @var string
	 */
	public $pkp;

	public function getCheckCodes(\FilipSedivy\EET\Receipt $receipt) {
		$codes = parent::getCheckCodes($receipt);
		$this->bkp = $codes['bkp']['_'];
		$this->pkp = base64_encode($codes['pkp']['_']);
		return $codes;
	}

}
