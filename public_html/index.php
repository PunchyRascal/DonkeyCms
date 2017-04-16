<?php

require __DIR__ . '/../bootstrap.php';

$app = new PunchyRascal\DonkeyCms\Application();

echo $app->getRouter()->route()->output();