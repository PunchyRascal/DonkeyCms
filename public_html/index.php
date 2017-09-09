<?php

require __DIR__ . '/../bootstrap.php';

echo $app->getRouter()->route()->output();