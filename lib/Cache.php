<?php

namespace PunchyRascal\DonkeyCms;

/**
 * Cache management - storing, retreiving
 */
class Cache {

	const CACHE_DIR = '../../../../cache/donkeyCms';

	/**
	 * Default time to live - seconds
	 * after this time, cached content is dropped
	 * @var int
	 */
	const TTL = 3600;

	/**
	 * @var Application
	 */
	private $app;

	public function __construct(Application $app) {
		$this->app = $app;
	}

	public function emptyCache($namespace) {
		$files = scandir($this->getFilePath($namespace, ''));
		foreach ($files AS $file) {
			if ($file === '.' OR $file === '..') {
				continue;
			}
			unlink($this->getFilePath($namespace, $file));
		}
	}

	private function getFilePath($namespace, $name) {
		$path = __DIR__ . '/' . self::CACHE_DIR . '/';
		if (!is_dir($path . $namespace)) {
			mkdir($path . $namespace, 0755, true);
		}
		return $path . "$namespace/$name";
	}

	/**
	 * Retrieve cached content based on prefix/namespace and parameters
	 * - if the cache does not exist, it can be optionally created with the $contentGenerator callback
	 * @param string $namespace Directory into which to store the cache
	 * @param array $params Cache is stored/retrieved under a name made by hashing these values
	 * @param callable $contentGenerator Generates the content if not cached
	 * @param int $ttl Expiration time (how old cache do I still accept?)
	 * @param callable $wakeup Optional callback to prepare cached data for use (eg. unserialize)
	 * @return string|null Cached content or NULL, content either cached from before or by calling the $contentGenerator
	 */
	public function getByParams(
		$namespace,
		array $params = [],
		callable $contentGenerator = null,
		$ttl = self::TTL,
		callable $wakeup = null
	) {
		$token = $this->getTokenForParams($params);
		$content = $this->get($namespace, $token, $ttl);

		if ($content === null AND $this->isCachingPrevented() AND $contentGenerator) {
			$content = $contentGenerator();
		} elseif ($content === null AND $contentGenerator) {
			$content = $this->set($namespace, $token, $contentGenerator());
		}

		return $wakeup ? $wakeup($content) : $content;
	}

	/**
	 * Builds cache token/name based on an array of parameters and prefix
	 * @param array $params
	 * @return string
	 */
	public function getTokenForParams($params) {
		return md5(join('_', $params));
	}

	/**
	 * Store content to cache, identified by name/token
	 * @param string $name The token/name identifying the cache
	 * @param string $content content to cache
	 * @return string stored content
	 */
	private function set($namespace, $name, $content) {
		file_put_contents($this->getFilePath($namespace, $name), $content);
		return $content;
	}

	/**
	 * Retrieve cached content
	 * @param string $name cached item name - its token
	 * @param int $ttl cache time to live in seconds (after this time cache is removed)
	 * @return mixed null if key is not found or admin is logged in, cache content otherwise
	 */
	private function get($namespace, $name, $ttl = self::TTL) {
		if ($this->isCachingPrevented()) {
			return null;
		}

		$file = $this->getFilePath($namespace, $name);

		if (file_exists($file)) {
			if (filemtime($file) > time() - $ttl) {
				return file_get_contents($file);
			}
			unlink($file);
		}

		return null;
	}

	/**
	 * @todo use config instead of url
	 * @return bool
	 */
	private function isCachingPrevented() {
		return $this->app->config->useCache === false OR Session::isAdminLogged();
	}

}
