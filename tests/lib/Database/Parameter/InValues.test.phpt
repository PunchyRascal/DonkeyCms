<?php

require __DIR__ . '/../../../bootstrap.php';

use Tester\Assert;
use PunchyRascal\DonkeyCms\Database\Parameter\InValues;

$db = provideDb();
$param = new InValues($db, array("abc", 7, "'", null));

Assert::same("'abc','7','\'',''", $param->getSafeSql());
