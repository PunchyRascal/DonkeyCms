<?php

namespace PunchyRascal\DonkeyCms\Recaptcha;

use PunchyRascal\DonkeyCms\Http;
use ReCaptcha\ReCaptcha;

trait VerificationTrait {

	abstract function getApp();

	private function checkCaptcha() {
		if ($this->getApp()->config->useCaptcha === false) {
			return true;
		}

		$recaptcha = new ReCaptcha($this->getApp()->config->recaptcha->secret);
		return $recaptcha
			->verify(Http::getPost('g-recaptcha-response'), Http::getServer('REMOTE_ADDR'))
			->isSuccess();
	}

}