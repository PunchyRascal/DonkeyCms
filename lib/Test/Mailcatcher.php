<?php

namespace PunchyRascal\DonkeyCms\Test;

/**
 * Mailcatcher
 */
class Mailcatcher {

	const BASE_URL = 'http://localhost:1080';

	public function __construct() {
		$this->removeAllMessages();
	}

	public function getMessage($email, $subject = null) {
		$messages = $this->getAllMessages();

		foreach ($messages AS $message) {
			if ($this->messageExists($message, $email, $subject)) {
				return $message;
			}
		}

		return null;
	}

	public function getMessageText($email, $subject = null) {
		$message = $this->getMessage($email, $subject);

		return $this->getSingleMessage($message->id);
	}

	private function getAllMessages() {
		return json_decode(file_get_contents(self::BASE_URL . '/messages'));
	}

	private function getSingleMessage($id) {
		return file_get_contents(self::BASE_URL . "/messages/$id.html");
	}

	private function messageExists($message, $email, $subject) {
		$recipientCheksOut = false;
		$subjectCheksOut = false;
		foreach ($message->recipients AS $recipient) {
			if ($recipient === "<$email>") {
				$recipientCheksOut = true;
				break;
			}
		}
		if ($subject === null OR $message->subject === $subject) {
			$subjectCheksOut = true;
		}
		return $subjectCheksOut AND $recipientCheksOut;
	}

	private function removeAllMessages() {
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, self::BASE_URL . '/messages');
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'DELETE');
		curl_exec($curl);
		curl_close($curl);
	}
}