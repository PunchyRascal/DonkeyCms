<?php

namespace PunchyRascal\DonkeyCms;

class Format {

	private static $scrambleTable = [
		1 => 'a4', 2 => 's4',
		3 => '5X', 4 => 'tu',
		5 => '96', 6 => '1k',
		7 => 'qA', 8 => 'f2',
		9 => '70', 0 => 'W8',
		'a' => 'a3', 'b' => 'c2',
		'c' => 'b7', 'd' => '58',
		'e' => 'v2', 'f' => '94',
		'g' => 'w3', 'h' => 'sd',
		'i' => 'r8', 'j' => 'lp',
		'k' => 'Z6', 'l' => 'ik',
		'm' => 'kk', 'n' => 'tt',
		'o' => 't1', 'p' => '2t',
		'q' => 'ee', 'r' => 'f4',
		's' => 'mm', 't' => 'h5',
		'u' => 'b2', 'v' => '5q',
		'w' => 'g8', 'x' => '66',
		'y' => 'l7', 'z' => 'o6',
	];

	public static function forHtml($data) {
		return htmlspecialchars($data, ENT_QUOTES);
	}

	public static function checkEmail($email) {
		$atom = '[-a-z0-9!#$%&\'*+\/=?^_`{|}~\.]'; // username
		$domain = '[a-z0-9]([-a-z0-9]{0,61}[a-z0-9])'; // domain
		return preg_match("/^$atom+(\\.$atom+)*@($domain?\\.)+$domain\$/i", $email) === 1;
	}

	public static function number($value, $precision = 0) {
		return number_format($value, $precision, ',', ' ');
	}

	/**
	 * @param string $value Text to shorten
	 * @param int $length
	 * @return string
	 */
	public static function shortenText($value, $length) {
		$text = htmlspecialchars(
			trim(
				trim(
					mb_substr(
						strip_tags(
							html_entity_decode($value), ENT_QUOTES
						),
						0,
						$length
					)
				),
				'.'
			),
			ENT_QUOTES
		);
		if (mb_strlen(trim($value)) > $length) {
			$text .= '&hellip;';
		}
		return $text;
	}

	public static function date($time) {
		if (strpos($time, '-') !== false) {
			if ($time == '0000-00-00 00:00:00') {
				return '-';
			}
			return date("j. n. Y H:i", strtotime($time));
		}
		else {
			return date("j. n. Y", $time);
		}
	}

	public static function scramble($input) {
		return strtr($input, self::$scrambleTable);
	}

	public static function unScramble($input) {
		return strtr($input, array_flip(self::$scrambleTable));
	}

	public static function filename($originalName) {
		$chars = "abcdefghijklmnopqrstuvwxyz0123456789";
		$charsCap = strtoupper($chars);
		$rand = str_shuffle($chars.$charsCap.rand(1, 1000000).time());
		$rand = str_shuffle($rand);
		$rand = substr($rand, 0, 10);
		$rand = str_shuffle($rand);
		$name = self::forFilename($originalName);
		$name = substr($name, -20);
		return $rand .'_'. strtolower($name);
	}

	public static function forFilename($string) {
		$string = preg_replace("/\s/i", "_", $string);
		$string = preg_replace("/[^a-z0-9_\-\.]*/i", "", $string);
		return $string;
	}

}