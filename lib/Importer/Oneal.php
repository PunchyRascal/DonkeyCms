<?php

namespace PunchyRascal\DonkeyCms\Importer;

class Oneal extends Base {

	private $data;

	public function getXml() {
		$conf = $this->app->config->oneal;
		$config = new \W2\Ecommerce\Api\Client\Config\ApiClientConfig(
			$conf->host, $conf->clientId, $conf->clientSecret
		);
		$storage = new \W2\Ecommerce\Api\Client\TokenStorage\FilesystemStorage();
		$client = new \W2\Ecommerce\Api\Client\ProductApiClient($config, $storage);

		if (!$client->isAuthenticated()) {
			try {
				$client->authenticate($conf->username, $conf->password);
			} catch (\W2\Ecommerce\Api\Exception\ApiAuthenticationException $e) {
				return $this->log->error('Authentication error - %s', $e->getMessage());
			}
		}

		$page = 1;
		$products = [];
		while (true) {
			$this->data = $client->getProducts($page * 200 - 200, $page * 200);
			if (!$this->data['result']['records'] OR $this->data['code'] !== 200) {
				break;
			}
			$products = array_merge($products, $this->data['result']['records']);
			$page++;
		}
		$this->data['result']['records'] = $products;

		$this->isValid = $this->data['code'] === 200 AND $this->data['result']['records'];
		if (!$this->isValid) {
			$this->log->error("Invalid data: %s", print_r($this->data, true));
		}
	}

	public function getItemBrand($item) {
		return 'Oneal';
	}

	public function getItemId($item) {
		return $item['product_number'];
	}

	public function getItemImgUrl($item) {
		if ($item['images']) {
			return $item['images'][0]['url'];
		}
		return '';
	}

	public function getItemName($item) {
		return $item['name'];
	}

	public function getItemPrice($item) {
		return $item['price']['with_vat'];
	}

	public function getItemStock($item) {
		$stock = (int) $item['stock']['local'] ?? 0;
		$stock += (int) $item['stock']['external'] ?? 0;
		return $stock ? 1 : 0;
	}

	public function getItemText($item) {
		$text = $item['description'] . '<table class="table table-striped">';
		foreach ($item['properties'] AS $key => $value) {
			$text .= "<tr><td>$key</td><td>$value</td></tr>";
		}
		return $text . '</table>';
	}

	public function getProducts() {
		return $this->data['result']['records'];
	}

}
