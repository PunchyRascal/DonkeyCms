<?php

require __DIR__ . '/../../../bootstrap.php';

use Tester\Assert;

$app = \Mockery::mock('PunchyRascal\DonkeyCms\Application')->makePartial();
$uploader = new PunchyRascal\DonkeyCms\Controller\Admin\Uploader($app);

Assert::equal('jpg', $uploader->getExtension('foo.jpg'));
Assert::equal('torrent', $uploader->getExtension('foo.torrent'));
Assert::equal('torrent', $uploader->getExtension('foo.boo.poo.torrent'));

Assert::equal('foo', $uploader->getName('foo.jpg'));
Assert::equal('boo', $uploader->getName('boo.jpeg'));
Assert::equal('boo.foo.moo', $uploader->getName('boo.foo.moo.jpeg'));
