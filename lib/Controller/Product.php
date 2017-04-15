<?php

namespace PunchyRascal\DonkeyCms\Controller;

use PunchyRascal\DonkeyCms\Http;
use PunchyRascal\DonkeyCms\Session;
use PunchyRascal\DonkeyCms\Cache;
use PunchyRascal\DonkeyCms\EmailMessage;

class Product extends Base {

	use \PunchyRascal\DonkeyCms\Recaptcha\VerificationTrait;

	private $id, $product, $images;

	public function execute() {
		$this->id = Http::getGet('id', Http::getPost('id'));

		if (!Http::getPost('itemQuery')) {
			return;
		}
		if (!Http::getPost('contact') OR !Http::getPost('text') OR !$this->checkCaptcha()) {
			Http::redirect("/?p=e-shop&id=$this->id&m=12");
		}

		$body = new \PunchyRascal\DonkeyCms\Template();
		$body->setFileName('email/productQuestion.twig')
			->setValue('product', Http::getPost('product'))
			->setValue('link', Http::getPost('link'))
			->setValue('contact', Http::getPost('contact'))
			->setValue('text', Http::getPost('text'));

		$mail = new EmailMessage();
		$mail->setFrom($this->app->config->emailFrom)
			->setTo($this->app->config->emailTo)
			->setSubject('Dotaz k produktu ' . $this->app->config->appName)
			->setBodyHtml($body->process());
		if ($mail->send()) {
			Http::redirect("/?p=e-shop&id=$this->id&m=15");
		}
		Http::redirect("/?p=e-shop&id=$this->id&m=16");
	}

	public function output() {
		return $this->cache->getByParams('productDetail', [$this->id, Http::getGet('m')], function () {
			return $this->outputWithoutCache();
		});
	}

	public function outputWithoutCache() {
		$this->product = $this->getProductData();
		if (!$this->product['id']) {
			$this->getTemplate()->setFileName('404.twig');
			Http::markResponseNotFound();
		} elseif (!$this->product['active']) {
			$this->getTemplate()->setFileName('productInactive.twig');
		} else {

			$this->getTemplate()->setFileName('product.twig');
		}
		$this->images = $this->db->query(
			"SELECT * FROM item_image WHERE home_art = %s",
			$this->id
		);

		$this->getTemplate()->data = array(
			'pageTitle' => $this->product['name'],
			'product' => $this->product,
			'productImages' => $this->images,
			'openGraphMeta' => [
				'url' => $this->app->config->appUrl . '/?p=e-shop&id=' . $this->id,
				'type' => 'product',
				'title' => $this->product['name'],
				'description' => strip_tags($this->product['desc']),
				'image' => $this->getProductImageUrl()
			]
		);
		$this->getTemplate()->setGlobalValue('currentCatId', $this->product['cat_id']);
		return parent::output();
	}

	private function getProductImageUrl() {
		if ($this->product['image_url']) {
			return $this->product['image_url'];
		} elseif ($this->images) {
			return $this->app->config->appUrl . '/item/'
				. $this->images[0]['home_art'] . '_'
				. $this->images[0]['art_img_count'] .'.'
				. $this->images[0]['extension'];
		}
	}

	private function getProductData() {
		return $this->db->getRow(
			"SELECT e_item.*, e_cat.name AS cat_name, e_cat.id AS cat_id,
				parent.name AS parentName, parent.id AS parentId
			FROM e_item
			INNER JOIN e_cat ON e_item.home = e_cat.id AND e_cat.active = 1
			LEFT JOIN e_cat AS parent ON parent.id = e_cat.parent_cat
			WHERE e_item.id = %s AND price > 0
			LIMIT 1",
			$this->id
		);
	}

}
