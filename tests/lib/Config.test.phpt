<?php

require __DIR__ . '/../bootstrap.php';

use Tester\Assert;
use PunchyRascal\DonkeyCms\Config;

$config = new Config(__DIR__ . '/test.json');

Assert::same('bar', $config->foo);
Assert::same(47, $config->key2);
Assert::same(null, $config->key3);
