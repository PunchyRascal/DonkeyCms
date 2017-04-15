<?php

namespace PunchyRascal\DonkeyCms;

/**
 * Makes objects more strict - disallow writing to uninitialized properties etc.
 */
class StrictObject {

	public function __set($name, $value) {
		throw new \InvalidArgumentException("Trying to store to uninitialized property '$name'");
	}

}
