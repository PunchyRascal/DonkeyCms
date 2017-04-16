<?php

require __DIR__ . "/../bootstrap.php";

$app = new PunchyRascal\DonkeyCms\Application();

$tree = new \PunchyRascal\DonkeyCms\Model\CategoryTraversal($app->db);

$tree->rebuildChildren();
