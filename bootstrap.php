<?php

ini_set('session.cookie_httponly', true);
ini_set('session.use_only_cookies', true);
session_set_cookie_params(65536, '/', null, true, true);
mb_internal_encoding("utf-8");
date_default_timezone_set('Europe/Prague');

require __DIR__ . "/vendor/autoload.php";

header('Encoding: utf-8');
header("Content-type: text/html; charset=utf-8");
header("X-Frame-Options: Deny");
header("Content-Security-Policy: frame-ancestors 'none'");

session_start();

ob_start();

$app = new PunchyRascal\DonkeyCms\Application();

require_once __DIR__ . '/customRoutes.php';
