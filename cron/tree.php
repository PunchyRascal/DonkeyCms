<?php

require __DIR__ . "/../bootstrap.php";

$tree = new \PunchyRascal\DonkeyCms\Model\CategoryTraversal($app->db);

$tree->rebuildChildren();
