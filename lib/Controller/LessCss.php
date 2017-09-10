<?php

namespace PunchyRascal\DonkeyCms\Controller;

use Less_Parser;
use PunchyRascal\DonkeyCms\Http;

class LessCss extends Base {

	public function output() {
		header("Content-type: text/css; charset=utf-8");
		header("Cache-Control: max-age=8640000");
		header_remove('Pragma');
		header_remove('Expires');
		return $this->cache->getByParams(
			'css',
			[Http::getGet('file'), Http::getGet('v')],
			[$this, 'parseCss'],
			10 * 24 * 3600
		);
	}

	public function parseCss() {
		$parser = new Less_Parser();
		$parser->parseFile($this->getFile());
		return $parser->getCss();
	}

	private function getFile() {
		return __DIR__ . '/../../../../../public_html/css/'
			. $this->validateFile(Http::getGet('file')) .'.less';
	}

	private function validateFile($filename) {
		if (preg_match("/^[a-z]+$/", $filename)) {
			return $filename;
		}
		throw new \Exception("Invalid file");
	}

}
