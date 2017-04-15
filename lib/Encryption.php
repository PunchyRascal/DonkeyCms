<?php

namespace PunchyRascal\DonkeyCms;

class Encryption {

	public static function hash($input) {
		return hash('sha256', $input);
	}

}