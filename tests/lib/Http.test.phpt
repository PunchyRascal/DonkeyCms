<?php

require __DIR__ . '/../bootstrap.php';

use Tester\Assert;
use PunchyRascal\DonkeyCms\Http;

class TestHttp extends Tester\TestCase {

	public function testPost() {
		$_POST['e'] = array(
			'test' => array(
				'sub' => array(
					'final' => 478
				)
			)
		);

		Assert::same(478, Http::getPost('e|test|sub|final'));
		Assert::same(null, Http::getPost('e|test|sub|final|a'));
		$_POST['d'] = 'osel';
		Assert::same('osel', Http::getPost('d'));
	}

	public function testFiles() {
		$_FILES = [
			'file' => [
				'tmp' => [
					'sub' => [
						'final' => 478
					]
				]
			]
		];
		Assert::same(null, Http::getFile('file|tmp|sub|final|a'));
		Assert::same(478, Http::getFile('file|tmp|sub|final'));
	}

	public function testCookie() {
		$_COOKIE['sysel'] = 748;
		Assert::same(748, Http::getCookie('sysel'));
	}
}
$testCase = new TestHttp;
$testCase->run();
