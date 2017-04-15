<?php

namespace PunchyRascal\DonkeyCms\Controller;

use PunchyRascal\DonkeyCms\Http;
use PunchyRascal\DonkeyCms\EmailMessage;

class Contact extends Base {

	use \PunchyRascal\DonkeyCms\Recaptcha\VerificationTrait;

	public function execute() {
		if (!Http::getPost('message_submit')) {
			return;
		}
		if (!Http::getPost('email') OR !Http::getPost('message') OR !$this->checkCaptcha()) {
			Http::redirect("?p=prodejna&m=12");
		}

		$body = new \PunchyRascal\DonkeyCms\Template();
		$body->setFileName('email/contactForm.twig')
			->setValue('name', Http::getPost('name'))
			->setValue('message', Http::getPost('message'))
			->setValue('email', Http::getPost('email'));

		$mail = new EmailMessage();
		$mail->setFrom($this->app->config->emailFrom)
			->setTo($this->app->config->emailTo)
			->setSubject('Vzkaz z ' . $this->app->config->appName)
			->setBodyHtml($body->process());

		$mail->send() ?
			Http::redirect("/?p=prodejna&m=15")
				: Http::redirect("/?p=prodejna&m=16");
	}

	public function output() {
		$page = $this->db->getRow("SELECT * FROM e_page WHERE url = 'kontakt' AND active = 1");
		$this->getTemplate()
			->setFileName('contact.twig')
			->setValue('googleMapsKey', $this->app->config->googleMapsKey)
			->setValue('page', $page)
			->setValue('pageTitle', isset($page['name']) ? $page['name'] : 'Kontakt');
		return parent::output();
	}

}
