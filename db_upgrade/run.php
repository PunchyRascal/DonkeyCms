<?php

if (!isset($argv[1]) OR ($argv[1] !== 'PRE' AND $argv[1] !== 'POST')) {
	die('Invalid upgrade script type given: "'. (isset($argv[1]) ? $argv[1] : '') .'"');
}

require __DIR__ . '/../bootstrap.php';

$type = $argv[1];
$db = $app->db;
$log = new \PunchyRascal\DonkeyCms\Logger(null, "$type DB upgrade");

if ($type === 'PRE') {
	$log->info('Starting PRE');
	require_once __DIR__ . '/pre.php';
	$log->info('Finished PRE');
}

if ($type === 'POST') {
	$log->info('Starting POST');
	require_once __DIR__ . '/post.php';
	$log->info('Finished POST');
}
