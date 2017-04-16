<?php

require __DIR__ . '/../bootstrap.php';

use Tester\Assert;

$log = new \PunchyRascal\DonkeyCms\Logger(PunchyRascal\DonkeyCms\Logger::TYPE_RETURN_OUTPUT);

Assert::match(
	'#^\d{4}-\d\d-\d\d \d\d:\d\d:\d\d INFO: Array \( \[foo\] => 78 \)$#',
	trim(preg_replace("/\s+/", ' ', $log->info(['foo' => 78])))
);

Assert::match(
	'#^\d{4}-\d\d-\d\d \d\d:\d\d:\d\d INFO: moo 78 boo$#',
	trim(preg_replace("/\s+/", ' ', $log->info('moo %s boo', 78)))
);

Assert::match(
	'#^\d{4}-\d\d-\d\d \d\d:\d\d:\d\d ERROR: moo 78 boo$#',
	trim(preg_replace("/\s+/", ' ', $log->error('moo %s boo', 78)))
);

Assert::match(
	'#^\d{4}-\d\d-\d\d \d\d:\d\d:\d\d INFO: moo Array \( \[0\] => 62 \) boo$#',
	trim(preg_replace("/\s+/", ' ', $log->info('moo %s boo', [62])))
);
