<?php

namespace PunchyRascal\DonkeyCms\Controller;

use PunchyRascal\DonkeyCms\Session;
use PunchyRascal\DonkeyCms\Model\Order;

class AjaxSideCart extends Base {

	public function output() {

		if (Session::get('orderId')) {
			$length = count(Order::getInstace($this->db, $this->app, Session::get('orderId'))->getItems());
		} else {
			$length = 0;
		}



		$this->getTemplate()
				->setFileName('base/ajaxSideCart.twig')
				->setValue('orderItemsLength', $length);
		return parent::output();
	}

}
