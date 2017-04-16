<?php

require __DIR__ . "/../bootstrap.php";

$runner = new \PunchyRascal\DonkeyCms\Importer\Runner(new \PunchyRascal\DonkeyCms\Application(), new \PunchyRascal\DonkeyCms\Logger('prod-import.log'));

if (isset($argv[1])) {
	$runner->runOnly($argv[1]);
}

if (isset($argv[2])) {
	$runner->runOnlyStock();
}

$runner->run();
