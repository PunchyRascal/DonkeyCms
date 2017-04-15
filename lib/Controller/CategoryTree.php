<?php

namespace PunchyRascal\DonkeyCms\Controller;

use PunchyRascal\DonkeyCms\Session;
use PunchyRascal\DonkeyCms\Http;
use PunchyRascal\DonkeyCms\Model\Category;

class CategoryTree extends Base {

	public function output() {
		return Category::getCategoryTree(
			$this->cache,
			$this->db,
			[
				'page' => substr(Http::getPost('page'), 0, 5) === 'admin' ? 'admin&amp;action=e_items' : 'e-shop',
				'activeCat' => Http::getPost('cat', 0),
				'activeOnly' => strpos(Http::getPost('page'), 'admin') !== false ? !Session::isAdminLogged() : true,
				'lineContent' => Http::getPost('lineContent'),
			]
		);
	}

}