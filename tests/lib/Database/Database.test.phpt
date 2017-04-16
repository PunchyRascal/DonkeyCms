<?php

require __DIR__ . '/../../bootstrap.php';

use Tester\Assert;

$db = provideDb();

Assert::same("bla", $db->escapeString('bla'));
Assert::same("'bla'", $db->escapeString('bla', true));
Assert::same("'bla'", $db->escapeString('bla', true, true));
Assert::same("bla", $db->escapeString('bla', false, true));

Assert::same("", $db->escapeString(''));
Assert::same("''", $db->escapeString('', true));
Assert::same("NULL", $db->escapeString('', true, true));
Assert::same("NULL", $db->escapeString('', false, true));

Assert::same("`54sdf6`", $db->escapeIdentifier('54sdf6ščřščšč-*/-*/š-*čř/šč    '));
Assert::same("`sys_as`", $db->escapeIdentifier('sys_as'));
Assert::same("`54deletefromsdf6`", $db->escapeIdentifier('54\'`-- delete from *.*;sdf6ščřščšč-*/-*/š-*čř/šč    '));

Assert::same(
	"SELECT * FROM sysel WHERE id = 'os\'el'",
	$db->formatQuery(["SELECT * FROM sysel WHERE id = %s", "os'el"])
);

Assert::same(
	"UPDATE sysel SET `osel` = 7, `sysel` = 987",
	$db->formatQuery(["UPDATE sysel SET %s", $db->createParam('COLUMNS', ['osel' => 7, 'sysel' => 987])])
);



