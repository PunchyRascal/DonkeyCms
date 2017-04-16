<?php

$mailcatcher = new \PunchyRascal\DonkeyCms\Test\Mailcatcher();
$_SESSION = [];
$app = new PunchyRascal\DonkeyCms\Application();
$app->db->query("DELETE FROM e_item WHERE id = 113323");
$app->db->query("DELETE FROM e_item WHERE id = 113324");
$app->db->query("DELETE FROM e_item WHERE id = 84690");

$app->db->query("DELETE FROM e_cat WHERE id = 8113323");
$app->db->query("INSERT INTO e_cat SET id = 8113323, name = 'CatFoo', active = 1, sequence = 100");
$app->db->query("INSERT INTO e_item SET
    id = 113323, price = 7000, name = 'Foo', `desc` = 'boo',
    stock = 1, make = 'noo', home = 8113323"
);

$I = new AcceptanceTester($scenario);
$I->wantTo('Buy a specific product');
$I->amOnPage('/?p=e-shop&id=113323');
$I->click('Koupit');
$I->canSee('Doprava i platba je pro vás ZDARMA');
$I->canSee('Česká pošta po ČR (0 Kč)');
$I->click('#Cart > tr.t-cart-item.t-product-id-113323 button.t-decrease-amount');

$app->db->query("INSERT INTO e_item SET
    id = 84690, price = 257, discount = 100, name = 'čep zadního tlumiče', `desc` = 'boo',
    stock = 1, make = 'noo', home = 8113323"
);

$I->amOnPage('/?p=e-shop&id=84690');
$app->db->query("UPDATE e_item SET price = 257, discount = 100 WHERE id = 84690");
$I->canSee('čep zadního tlumiče', 'h1');
$I->canSee('157 Kč');
$I->click('Koupit');

$I->canSee("Nákupní košík", 'h1');
$I->canSee('CELKEM:');
$I->canSee('157 Kč', '.t-total-price');
$I->click('#Cart > tr.t-cart-item.t-product-id-84690 button.t-increase-amount');

$I->canSee('314 Kč', '.t-total-price');
$I->click('#Cart > tr.t-cart-item.t-product-id-84690 button.t-decrease-amount');

$I->canSee('157 Kč', '.t-total-price');
$I->selectOption('delivery', 'cz_post');
$I->click('#Content form > noscript > input[type="submit"]');

$I->selectOption('payment', 'cod');
$I->click('#Content form > noscript > input[type="submit"]');

$app->db->query("INSERT INTO e_item SET
    id = 113324, price = 890, name = 'Odyssey Headset 45/45 integrované hlavové složení černé', `desc` = 'boo',
    stock = 1, make = 'noo', home = 8113323"
);

$I->amOnPage('/?p=e-shop&cat=8113323&sortby=4');
$I->click('#Content > div > div:nth-child(4) > div:nth-child(2) > div > div.buyMe > form > input.btn.btn-default');
$I->canSee("Nákupní košík", 'h1');
$I->canSee('Odyssey Headset 45/45 integrované hlavové složení černé', '.t-cart-item');

$I->click('Objednat');

$I->canSee('Kontaktní údaje', 'h1');
$I->fillField('name', 'Seymour Butts');
$I->fillField('street', 'Foo street 47');
$I->fillField('town', 'Codeville');
$I->fillField('zip', 'A1468');
$email = 'foo' . rand(1, 999999) . '@example.com';
$I->fillField('email', $email);
$I->fillField('phone', '+420321654978');
$I->fillField('note', 'Aeneas nominor, fama super stellas elevor');
$I->click('Rekapitulace objednávky »');

$I->canSee('Rekapitulace objednávky', 'h1');
$I->click('Závazně objednat');

$I->canSee('Objednávka dokončena', 'h1');

$publicId = $I->grabTextFrom('#tOrderId');
$orderData = $app->db->getRow("SELECT * FROM e_order WHERE public_id = %s", $publicId);
$orderId = $orderData['id'];

$I->canSee("Informace o této objednávce (č. $publicId) jsme Vám poslali na e-mail.");

$I->assertNotNull($orderData['date']);
unset($orderData['date']);
$I->assertNotNull($orderData['status_change_date']);
unset($orderData['status_change_date']);
$I->assertNotNull($orderData['id']);
unset($orderData['id']);
$I->assertNotNull($orderData['submission_date']);
unset($orderData['submission_date']);

$I->assertEquals(
    [
        'public_id' => $publicId,
        'name' => 'Seymour Butts',
        'street' => 'Foo street 47',
        'town' => 'Codeville',
        'zip' => 'A1468',
        'email' => $email,
        'phone' => '+420321654978',
        'note' => 'Aeneas nominor, fama super stellas elevor',
        'order' => null,
        'status' => 1,
        'status_note' => null,
        'delivery' => 'cz_post',
        'payment' => 'cod',
        'zasilkovna_branch' => null,
        'zasilkovna_id' => null,
        'price_items' => 890 + 157,
        'price_delivery' => 110,
        'price_payment' => 40,
        'price_total' => 890 + 157 + 110 + 40,
        'vat_rate' => \PunchyRascal\DonkeyCms\Application::VAT_RATE,
	],
    $orderData
);

$I->assertEquals(
    [
        [
            product_id  => 84690,
            product_name => 'čep zadního tlumiče',
            amount      => 1,
            price       => 157,
            variant     => null,
        ],
        [
            product_id  => 113324,
            product_name => 'Odyssey Headset 45/45 integrované hlavové složení černé',
            amount      => 1,
            price       => 890,
            variant     => null,
        ],
    ],
    $app->db->query("SELECT product_id, product_name, amount, price, variant
        FROM e_order_item WHERE order_id = %s ORDER BY product_name", $orderId)
);

$confirmationMail = $mailcatcher->getMessage($email, 'Potvrzení objednávky z '. $app->config->appName);
$confirmationMailText = $mailcatcher->getMessageText($email, 'Potvrzení objednávky z '. $app->config->appName);

$I->assertNotNull(
	$confirmationMail,
	'Confirmation email is sent to customer'
);

$I->assertContains(
	'Provedená objednávka na '.$app->config->appName.' č. ' . $publicId,
	$confirmationMailText
);

$I->assertContains(
	'ID Objednávky: ' . $publicId,
	$confirmationMailText
);
