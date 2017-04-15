<?php

namespace PunchyRascal\DonkeyCms;

/**
 * Working with GET, POST params, redirecting and more
 */
class Http {

	const STATUS_MOVED_TEMPORARILY = 302;
	const STATUS_MOVED_PERMANENTLY = 301;
	const STATUS_NOT_FOUND = 404;

	public static function markResponseNotFound() {
		http_response_code(self::STATUS_NOT_FOUND);
	}

	public static function redirect($url, $status = self::STATUS_MOVED_TEMPORARILY) {
		header("Location: $url", true, $status);
		exit;
	}

	public static function getGet($name, $default = null) {
		if (isset($_GET[$name])) {
			return $_GET[$name];
		}
		return $default;
	}

	public static function getCookie($name, $default = null) {
		if (isset($_COOKIE[$name])) {
			return $_COOKIE[$name];
		}
		return $default;
	}

	public static function setCookie($name, $value) {
		setcookie($name, $value, time() + 24 * 3600 * 30, '/');
	}

	public static function getServer($name, $default = null) {
		if (isset($_SERVER[$name])) {
			return $_SERVER[$name];
		}
		return $default;
	}

	public static function getFile($key) {
		$keys = explode('|', $key);
		$return = $_FILES;
		foreach ($keys AS $key) {
			if (isset($return[$key])) {
				$return = $return[$key];
			}
			else {
				return null;
			}
		}
		return $return;
	}

	public static function getPost($key = false, $default = null) {
		if ($key === false) {
			return $_POST;
		}
		$keys = explode('|', $key);
		$return = $_POST;
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

}