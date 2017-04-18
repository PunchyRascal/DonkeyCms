<?php

$_SESSION = [];

$mailcatcher = new \PunchyRascal\DonkeyCms\Test\Mailcatcher();

$I = new AcceptanceTester($scenario);
$app = new PunchyRascal\DonkeyCms\Application();
$app = new PunchyRascal\DonkeyCms\Application();
$app->db->query("DELETE FROM e_item WHERE home = 8113323");
$app->db->query("DELETE FROM e_cat WHERE id = 8113324");
$app->db->query("DELETE FROM e_cat WHERE id = 8113323");

$app->db->query("INSERT INTO e_cat SET id = 8113323, name = 'CatFoo', active = 1, sequence = 100");
$app->db->query("INSERT INTO e_item SET
    id = 113323, price = 7000, name = 'Anger chráničová vesta šedá L', `desc` = 'boo',
    stock = 1, make = 'noo', home = 8113323"
);

$I->wantTo('Send a question regarding a product');
$I->amOnPage('/?p=e-shop&id=113323');
$I->canSee('Anger chráničová vesta šedá L', 'h1');
$I->canSee('Máte dotaz?', 'h3');

$I->fillField('contact', 'Seymour Butts');
$I->fillField('text', 'What is the meaning of life?');
$I->click('Odeslat dotaz');

$mail = $mailcatcher->getMessageText($app->config->emailTo, 'Dotaz k produktu ' . $app->config->appName);

$I->assertRegExp("/Seymour Butts/", $mail);
$I->assertRegExp("/Anger chráničová vesta šedá L/", $mail);
$I->assertRegExp("/What is the meaning of life\?/", $mail);