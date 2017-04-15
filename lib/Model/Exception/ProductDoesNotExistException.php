<?php

namespace PunchyRascal\DonkeyCms\Model\Exception;

class ProductDoesNotExistException extends \PunchyRascal\DonkeyCms\Exception {

	public function __construct($productId) {
		parent::__construct("Product '$productId' does not exist.");
	}

}
