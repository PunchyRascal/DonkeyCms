<?php

$_SESSION = [];

$I = new AcceptanceTester($scenario);
$app = new PunchyRascal\DonkeyCms\Application();
$app->db->query("DELETE FROM e_item WHERE home = 8113323");
$app->db->query("DELETE FROM e_cat WHERE id = 8113324");
$app->db->query("DELETE FROM e_cat WHERE id = 8113323");

$app->db->query("INSERT INTO e_cat SET id = 8113323, name = 'Lovely category', active = 1, sequence = 100");
$app->db->query("INSERT INTO e_cat SET id = 8113324, name = 'Blue subcategory', active = 1, sequence = 100, parent_cat = 8113323");

for ($i = 0; $i < 30; $i++) {
	$app->db->query("INSERT INTO e_item SET
		id = 113323$i, price = 7000, name = 'Foo', `desc` = 'boo',
		stock = 1, make = 'noo', home = 8113323"
	);
}

$I->wantTo('See a list of products');
$I->amOnPage('/?p=e-shop&cat=8113323');
$I->canSee('Lovely category', 'h1');
$I->canSee('Blue subcategory', '#subCategories');

$I->assertEquals(
	'/?p=e-shop&cat=8113324',
	$I->grabAttributeFrom('#subCategories > div.item.editObject > a.main', 'href')
);

$I->assertRegExp(
	'/^30 položek$/',
	$I->grabTextFrom('div.t-ProductCount')
);

$I->canSeeElement('ul.pagination');

$I->assertEquals(
	'/?p=e-shop&cat=8113323&from=18',
	$I->grabAttributeFrom('#Content nav > ul > li > a.t-nextPageLink', 'href')
);

$I->assertEquals(
	'Další stránka »',
	$I->grabTextFrom('#Content nav > ul > li > a.t-nextPageLink', 'href')
);

