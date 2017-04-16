<?php

require __DIR__ . '/../../../bootstrap.php';

use Tester\Assert;
use PunchyRascal\DonkeyCms\Database\Parameter\ColumnValues;

$db = provideDb();
$param = new ColumnValues($db, array("sysel 47 ``'" => "value'_ 8"));

Assert::same("`sysel47` = 'value\'_ 8'", $param->getSafeSql());

$param = new ColumnValues($db, ["sysel1" => NULL]);
Assert::same("`sysel1` = NULL", $param->getSafeSql());
