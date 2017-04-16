<?php

require __DIR__ . '/../bootstrap.php';

$_SESSION = array();

use Tester\Assert;
use PunchyRascal\DonkeyCms\Session;

$_SESSION['e'] = array('test' => array('sub' => 7));
Assert::same(7, Session::get('e|test|sub'));
Assert::same(null, Session::get('eeee|test|sub'));

Session::set('d', 'osel');
Assert::same('osel', Session::get('d'));

Session::set('a|b|c', 'sysel2');
Session::set('a|b|c', 'sysel');
Assert::same('sysel', Session::get('a|b|c'));
Assert::same(7, Session::get('e|test|sub'));

$_SESSION['foo']['boo'] = [
	74 => 'baaa',
	89 => 'mooo'
];
Assert::same(Session::get('foo|boo|74'), 'baaa');
Assert::same(Session::delete('foo|boo|74'), 'baaa');
Assert::null(Session::get('foo|boo|74'));
Assert::same(Session::get('foo|boo|89'), 'mooo');

Session::set('foo|boo|poo|boo', 124);
Assert::same(Session::get('foo|boo|poo|boo'), 124);
Assert::null(Session::delete('xxx|444'));

$_SESSION = array();

Session::set('kk|boo', 'moo');
Assert::same('moo', Session::get('kk|boo'));
Assert::same('moo', $_SESSION['kk']['boo']);
Session::delete('kk|boo');
Assert::null(Session::get('kk|boo'));
