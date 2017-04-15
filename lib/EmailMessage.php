<?php

namespace PunchyRascal\DonkeyCms;

/**
 * Convenience wrapper around PHPMailer
 */
class EmailMessage {

	/**
	 * @var \PHPMailer
	 */
	public $mail;

	public function __construct() {
		$this->mail = new \PHPMailer(true);
		$this->mail->CharSet = 'utf-8';
	}

	public function setFrom($from) {
		$address = $this->mail->parseAddresses($from)[0];
		$this->mail->setFrom($address['address'], $address['name']);
		return $this;
	}

	public function setTo($to) {
		$addresses = $this->mail->parseAddresses($to);
		foreach ($addresses AS $address) {
			$this->mail->addAddress($address['address'], $address['name']);
		}
		return $this;
	}

	public function setBodyPlainText($text) {
		$this->mail->isHTML(false);
		$this->mail->Body = $text;
		return $this;
	}

	public function setBodyHtml($html) {
		$this->mail->isHTML(true);
		$this->mail->Body = $html;
		return $this;
	}

	public function setSubject($subject) {
		$this->mail->Subject = $subject;
		return $this;
	}

	public function send() {
		return $this->mail->send();
	}

}
