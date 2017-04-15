<?php

namespace PunchyRascal\DonkeyCms\Importer;

class Config {

	public static $shops = [
		[
			'class' => Katmar::class,
			'url' => 'http://www.dartmoor-bikes.cz/rss/zakaznik.xml',
			'origin' => 'dartmoor',
			'minProducts' => 400,
		],
		[
			'class' => BpLumen::class,
			'url' => 'http://bplumen.cz/xml/85f007f8c50dd25f5a45fca73cad64bd/73692484.html',
			'origin' => 'bplumen',
			'minProducts' => 3000,
		],
		[
			'class' => Katmar::class,
			'url' => 'http://www.katmar.cz/rss/zakaznik.xml',
			'origin' => 'katmar',
			'minProducts' => 1000,
		],
		[
			'class' => TbbBike::class,
			'url' => 'http://shop.tbb-bike.cz/xml/tbb_b2b.xml',
			'origin' => 'tbbbike',
			'minProducts' => 500,
		],
		[
			'class' => Bikestrike::class,
			'url' => 'http://www.bikestrike.com/Services/Feed.ashx?type=dodavatelsky.cz&v=2&key=[1]',
			'stockUrl' => 'http://www.bikestrike.com/Services/Feed.ashx?type=heureka.cz&key=&avail=1',
			'origin' => 'bikestrike',
			'minProducts' => 2000,
		],
		[
			'class' => UltimateBikes::class,
			'url' => 'https://www.ultimatebikes.cz/partner-f8c1f23d6a8d8d7904fc0ea8e066b3bb.xml',
			'origin' => 'ultimatebikes',
			'minProducts' => 200,
		],
		[
			'class' => Cyklozitny::class,
			'url' => 'http://www.cyklozitny.cz/partners/full/1/0d74ae0f08fa95d2cf6e7ddde8400b79/',
			'stockUrl' => 'http://www.cyklozitny.cz/partners/availability/1/0d74ae0f08fa95d2cf6e7ddde8400b79/',
			'origin' => 'cyklozitny',
			'minProducts' => 800,
		],
		[
			'class' => Cykloprofi::class,
			'url' => 'http://www.cykloprofi.inshop.cz/inshop/robots/cykloprofi_products.xml',
			'origin' => 'cykloprofi',
			'minProducts' => 1000,
		],
		[
			'class' => Oneal::class,
			'url' => null,
			'origin' => 'oneal',
			'minProducts' => 1000,
		],
		[
			'class' => Dema::class,
			'url' => 'https://eshop.dema.bike/api/export/73692484',
			'origin' => 'dema',
			'minProducts' => 1000,
		],
		[
			'class' => Progress::class,
			'url' => 'http://eshop.progresscycle.cz/zboziseznam.txt',
			'stockUrl' => 'http://eshop.progresscycle.cz/zbozidostupnost.txt',
			'origin' => 'progress',
			'minProducts' => 1000,
		],
		[
			'class' => MuckyNutz::class,
			'url' => 'https://www.muckynutz.cz/fotky26894/xml/zbozi_cz_new.xml',
			'origin' => 'muckynutz',
			'minProducts' => 30
		],
		[
			'class' => Snowbitch::class,
			'url' => 'http://www.snowbitch.cz/export/velkoobchod/export.xml',
			'origin' => 'snowbitch',
			'minProducts' => 5000
		]
	];

}