<?php

namespace PunchyRascal\DonkeyCms;

/**
 * Session management
 */
class Session {

	public static function isAdminLogged() {
		return self::get('adminLogged') === true;
	}

	public static function get($key, $default = null) {
		$keys = explode('|', $key);
		$return = $_SESSION;
		foreach ($keys AS $key) {
			if (isset($return[$key])) {
				$return = $return[$key];
			}
			else {
				return $default;
			}
		}
		return $return;
	}

	public static function set($key, $value) {
		$keys = explode('|', $key);
		$ref = &$_SESSION;
		$count = count($keys);
		$i = 1;
		foreach ($keys AS $key) {
			$last = $count === $i;
			if (!isset($ref[$key])) {
				$ref[$key] = [];
			}
			if ($last) {
				$ref[$key] = $value;
				return;
			}
			$ref = &$ref[$key];
			$i++;
		}
	}

	/**
	 * Unsets key/value from session
	 * @param string $key In the form of foo|boo|moo
	 *		which is equivalent to $_SESSION[foo][boo][moo]
	 * @return mixed
	 */
	public static function delete($key) {
		$keys = explode('|', $key);
		$index = 1;
		$ref = &$_SESSION;
		$count = count($keys);
		foreach ($keys AS $key) {
			$last = $index === $count;
			if (!isset($ref[$key])) {
				return null;
			}
			if ($last) {
				$value = $ref[$key];
				unset($ref[$key]);
				return $value;
			}
			$ref = &$ref[$key];
			$index++;
		}
	}

}