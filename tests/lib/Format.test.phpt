<?php

require __DIR__ . '/../bootstrap.php';

use PunchyRascal\DonkeyCms\Format;
use Tester\Assert;

Assert::true(Format::checkEmail("sysel@SEZNAM.cz"));
Assert::true(Format::checkEmail("sysel@yahoo.cz"));
Assert::false(Format::checkEmail("syselyahoo.cz"));

Assert::same(Format::scramble('14578'), 'a4tu96qAf2');
Assert::same(Format::unScramble('a4tu96qAf2'), '14578');
Assert::same(Format::scramble('1'), 'a4');
Assert::same(Format::unScramble('f2'), '8');
Assert::same(Format::scramble(''), '');
Assert::same(Format::scramble('sysel5'), 'mml7mmv2ik96');

Assert::match('#^[a-z0-9]{10}_sys_el\.jpg$#i', Format::filename('š*/+čšřžřážřý###áíýá=%sys e*l.j=˛˛pg'));
