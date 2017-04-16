<?php

namespace PunchyRascal\DonkeyCms\Controller\Admin;

use PunchyRascal\DonkeyCms\Http;

class Banner extends Base {

	public function execute() {
		if (!Http::getPost('banner_submit') AND !Http::getGet('banner_reset')) {
			return;
		}

		if (Http::getGet('banner_reset')) {
			copy(
				__DIR__. '/../../../public_html/images/banner-default.png',
				__DIR__ .'/../../../public_html/images/banner.png'
			);
			Http::redirect("/?p=admin&action=banner&m=1");
		}
		else {
			if (Http::getFile('banner_file|tmp_name')) {
				copy(
					Http::getFile('banner_file|tmp_name'),
					__DIR__ .'/../../../public_html/images/banner.png'
				);
			}
			Http::redirect("/?p=admin&action=banner&m=1");
		}
	}

	public function output() {
		$this->getTemplate()->setFileName('admin/banner.twig');
		return parent::output();
	}

}
