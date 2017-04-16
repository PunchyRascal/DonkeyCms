<?php
require __DIR__ . '/../bootstrap.php';

use Tester\Assert;
use PunchyRascal\DonkeyCms\Cache;

$cacheDir = __DIR__ . '/../../cache/donkeyCms/';

function rrmdir($dir) {
	if (is_dir($dir)) {
		$objects = scandir($dir);
		foreach ($objects as $object) {
			if ($object === "." || $object === "..") {
				continue;
			}
			if (is_dir("$dir/$object")) {
				rrmdir("$dir/$object");
			} else {
				unlink("$dir/$object");
			}
		}
		rmdir($dir);
	}
}
rrmdir($cacheDir);

class CacheTest extends Tester\TestCase {

	private $cacheDir = '/../../cache/donkeyCms/foo-namespace/';

	/**
	 * @var Mockery\Mock
	 */
	private $session, $app;
	/**
	 * @var stdClass
	 */
	private $appConfig;
	private $cache;

	protected function setUp() {
		$this->session = \Mockery::mock('alias:PunchyRascal\DonkeyCms\Session');
		$this->session->shouldReceive('isAdminLogged')->andReturn(false)->byDefault();
		$_SERVER = [];
		$this->app = \Mockery::mock('alias:PunchyRascal\DonkeyCms\Application');
		$this->appConfig = new stdClass();
		$this->app->config = $this->appConfig;
		$this->cache = new Cache($this->app);
	}

	protected function tearDown() {
		@unlink(__DIR__ . $this->cacheDir . 'foo-name');
		Mockery::close();
	}

	public function testSetGet() {
		$this->appConfig->useCache = true;
		$this->cache->getByParams('foo-namespace', ['foo-name'], function () { return 'foo-content'; });
		Assert::same('foo-content', $this->cache->getByParams('foo-namespace', ['foo-name']));

		touch(__DIR__ . $this->cacheDir . md5('foo-name'), time() - 3600 * 2);

		// we accept (default) hour long cache => no data
		Assert::null($this->cache->getByParams('foo-namespace', ['foo-name']));
	}

	public function testCustomExpirationTime() {
		$this->appConfig->useCache = true;
		$this->cache->getByParams('foo-namespace', ['foo-name'], function () { return 'foo-content'; });
		Assert::same('foo-content', $this->cache->getByParams('foo-namespace', ['foo-name']));

		touch(__DIR__ . $this->cacheDir . md5('foo-name'), time() - 3600 * 2);

		// we accept up to 3 hours old cache
		Assert::same('foo-content', $this->cache->getByParams('foo-namespace', ['foo-name'], null, 3600 * 3));
		// default cache ttl - gets nothing, removes cache file
		Assert::null($this->cache->getByParams('foo-namespace', ['foo-name']));
		// and now the cache is lost
		Assert::null($this->cache->getByParams('foo-namespace', ['foo-name'], null, 3600 * 3));
	}

	public function testDoesNotCacheIfTurnedOff() {
		$this->app->config->useCache = false;
		$this->cache->getByParams('foo-namespace', ['foo-name'], function () { return 'foo-content'; });
		Assert::null($this->cache->getByParams('foo-namespace', ['foo-name']));
	}

	public function testDoesNotCacheForAdmin() {
		$this->appConfig->useCache = true;
		$this->session->shouldReceive('isAdminLogged')->andReturn(true);
		$this->cache->getByParams('foo-namespace', ['foo-name'], function () { return 'foo-content'; });
		Assert::null($this->cache->getByParams('foo-namespace', ['foo-name']));

		// this does not store the value (to prevent admin-only html to show for other users)
		$this->cache->getByParams('foo-namespace', [47], function () {return 'cached';});
		Assert::true(
			!file_exists(__DIR__ . $this->cacheDir . $this->cache->getTokenForParams([47]))
		);
		Assert::same(
			'not-cached',
			$this->cache->getByParams('foo-namespace', [47], function () {return 'not-cached';})
		);
	}

	public function testGetByParams() {
		$this->appConfig->useCache = true;
		Assert::null(
			$this->cache->getByParams('foo-namespace', ['foo-param'])
		);
		Assert::same(
			'foo-content-params', $this->cache->getByParams('foo-namespace', ['foo-param'], function () {
				return 'foo-content-params';
			})
		);
		Assert::same(
			'foo-content-params', $this->cache->getByParams('foo-namespace', ['foo-param'])
		);
		Assert::null(
			$this->cache->getByParams('foo-namespace', ['foo-param', 74])
		);
	}

	public function testUnsetByParams() {
		$this->appConfig->useCache = true;
		Assert::same(
			'initial-value',
			$this->cache->getByParams('foo-namespace', ['foo-param', 74], function () {return 'initial-value';})
		);
		Assert::same(
			'initial-value',
			$this->cache->getByParams('foo-namespace', ['foo-param', 74])
		);
		$this->cache->emptyCache('foo-namespace');
		Assert::null($this->cache->getByParams('foo-namespace', ['foo-param', 74]));
	}

}
$testCase = new CacheTest;
$testCase->run();
