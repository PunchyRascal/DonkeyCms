<?php

require __DIR__ . '/../bootstrap.php';

use Tester\Assert;

$app = \Mockery::mock('PunchyRascal\DonkeyCms\Application')->makePartial();

$app->config = new stdClass();

$app->config->isProductionMode = false;
Assert::false($app->useProductionFeatures());

$app->config->isProductionMode = true;
Assert::true($app->useProductionFeatures());

