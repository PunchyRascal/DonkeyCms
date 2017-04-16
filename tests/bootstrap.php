<?php

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../bootstrap.php';

Tester\Environment::setup();

use PunchyRascal\DonkeyCms\Database\Database;
use PunchyRascal\DonkeyCms\Config;
$conf = new Config(__DIR__ . '/../config.json');
$db = new Database(
	$conf->db->server,
	$conf->db->user,
	$conf->db->password,
	$conf->db->name
);
function provideDb() {
	return $GLOBALS['db'];
}
