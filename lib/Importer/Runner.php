<?php

namespace PunchyRascal\DonkeyCms\Importer;

/**
 * @todo write tests!!!
 */
class Runner {

	CONST LOCK_FILE = 'importer.lock';

	/**
	 * @var \PunchyRascal\DonkeyCms\Application
	 */
	private $app;

	private $runOnly;
	private $onlyStock = false;

	/**
	 * @var \PunchyRascal\DonkeyCms\Logger
	 */
	private $log;

	/**
	 * @var array List of imported feeds/shops
	 **/
	private $shops;

	public function __construct(\PunchyRascal\DonkeyCms\Application $app, \PunchyRascal\DonkeyCms\Logger $log) {
		if (file_exists(__DIR__ . '/../../cron/'. self::LOCK_FILE)) {
			throw new \Exception("Lock file exists. Aborting import.");
		}
		touch(__DIR__ . '/../../cron/' .self::LOCK_FILE);
		$this->app = $app;
		$this->log = $log;
		$this->shops = $this->app->db->query("SELECT * FROM e_product_import_setup WHERE active = 1");
	}

	public function __destruct() {
		unlink(__DIR__ . '/../../cron/' . self::LOCK_FILE);
		$this->log->info("Peak memory: " . round(memory_get_peak_usage() / 1024 / 1024, 2) . ' MiB');
		$this->log->info("*import finished*");
	}

	public function runOnly($shopName) {
		$this->runOnly = $shopName;
		$this->log->info("Import feed name filter: $shopName");
	}

	public function runOnlyStock() {
		$this->onlyStock = true;
		$this->log->info("Import run only stock feed");
	}

	public function run() {
		foreach ($this->shops AS $config) {
			if ($this->runOnly AND $this->runOnly != $config['id']) {
				$this->log->info("Skipping %s", $config['id']);
				continue;
			}

			/* @var $importer \PunchyRascal\DonkeyCms\Importer\Base */
			$class = __NAMESPACE__ .'\\'. $config['importer_class'];
			$importer = new $class($this->app, $this->log, $config);

			$this->log->info("%s: start processing, priceFactor=%s", $config['id'], $importer->priceFactor);

			$importer->getXml();

			$this->app->db->query(
				"UPDATE e_product_import_setup SET last_import_date = NOW() WHERE id = %s",
				$importer->origin
			);

			$this->log->info("%s: Is valid: %s", $config['id'], $importer->isValid ? 1 : 0);

			if ($this->onlyStock) {
				$this->log->info("%s: Skipping products import", $importer->origin);
			} elseif (!$this->processFeed($importer)) {
				continue;
			}

			if ($importer->stockUrl) {
				$this->log->info("%s: Start stock feed", $importer->origin);
				$this->processStockFeed($importer);
			}
		}
	}

	private function processFeed(Base $importer) {
		if ($importer->isValid) {
			return $this->importShop($importer);
		} else {
			$this->log->error("%s: Invalid products feed data", $importer->origin);
			return false;
		}
	}

	private function processStockFeed(Base $importer) {
		$importer->getStockXml();
		if ($importer->isStockValid) {
			$count = 1;
			foreach ($importer->getStocks() AS $item) {
				$this->app->db->query(
					"UPDATE e_item SET `stock` = %s WHERE origin = %s AND external_id = %s",
					$importer->getItemSpecialStock($item),
					$importer->origin,
					$importer->getItemSpecialId($item)
				);
				if ($this->app->db->error()) {
					$this->log->error("%s: stock feed DB error: ", $importer->origin, $this->app->db->error());
				}
				$count++;
			}
			$this->log->info("%s: Stock feed items updated: %d", $importer->origin, $count);
		} else {
			$this->log->error("%s: Invalid stock feed data", $importer->origin);
		}
	}

	private function importShop(Base $importer) {
		$products = $importer->getProducts();

		$this->log->info("%s: Products count: %d", $importer->origin, count($products));

		if (count($products) < $importer->minProducts) {
			$this->log->error(
				"%s: Too small amount of products, expected at least %d; aborting",
				$importer->origin,
				$importer->minProducts
			);
			return false;
		}

		$this->app->db->query(
			"UPDATE e_item SET import_status = 'old' WHERE origin = %s AND auto_update = 1",
			$importer->origin
		);

		foreach ($products AS $item) {
			$autoUpdate = $this->app->db->getRow(
				"SELECT `id`, `auto_update` FROM `e_item` WHERE `origin` = %s AND `external_id` = %s",
				$importer->origin,
				$importer->getItemId($item)
			);
			if ($autoUpdate['id'] AND !$autoUpdate['auto_update']) {
				continue;
			}
			$this->updateProduct($importer, $item);
		}

		$this->app->db->query("DELETE FROM e_item WHERE import_status = 'old' AND origin = %s", $importer->origin);
		$this->log->info("%s: Deleted items: %d", $importer->origin, $this->app->db->affectedRows());

		return true;
	}

	private function updateProduct(Base $importer, $item) {
		$this->app->db->query(
			'INSERT INTO e_item
				SET
					`name` = %1$s,
					`desc` = %2$s,
					`external_id` = %3$s,
					`origin` = %4$s,
					`make` = %5$s,
					`image_url` = %6$s,
					`stock` = %7$s,
					`price` = %8$s,
					`availability` = %9$s,
					`availability_days` = %10$s,
					`import_resolved` = 0,
					`active` = 0
				ON DUPLICATE KEY UPDATE
					`name` = %1$s,
					`desc` = %2$s,
					`make` = %5$s,
					`image_url` = %6$s,
					`stock` = %7$s,
					`availability` = %9$s,
					`availability_days` = %10$s,
					`price` = %8$s,
					`import_status` = NULL
			',
			$importer->getItemName($item),
			$importer->getItemText($item),
			$importer->getItemId($item),
			$importer->origin,
			$importer->getItemBrand($item),
			$importer->getItemImgUrl($item),
			$importer->getItemStock($item) > 0 ? $importer->getItemStock($item) : 0,
			round($importer->getItemPrice($item) * $importer->priceFactor),
			$importer->getAvailabilityDescription($item),
			$importer->getAvailabilityInDays($item)
		);

		if ($this->app->db->error()) {
			$this->log->error($this->app->db->error());
		}
	}

}
