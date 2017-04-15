<?php

namespace PunchyRascal\DonkeyCms\Model;

use PunchyRascal\DonkeyCms\EmailMessage;
use PunchyRascal\DonkeyCms\Model\Exception\ProductDoesNotExistException;
use PunchyRascal\DonkeyCms\Application;
use PunchyRascal\DonkeyCms\Database\Database;

/**
 * Contract between user and seller
 * @property-read string $date				 Date the order was placed
 * @property string		 $name				 Name and surname of the client
 * @property string		 $street			 Street where to ship
 * @property string		 $town				 City where to ship
 * @property string 	 $zip				 Postal code where to ship
 * @property string 	 $email				 Email of the client
 * @property string 	 $phone				 Phone of the client
 * @property text		 $note				 Client note upon placing the order
 * @property text		 $order				 Items of the order
 * @property int		 $status			 Status of the order
 * @property text		 $status_note		 Admins' note
 * @property string 	 $delivery			 Method of delivery
 * @property string 	 $payment			 Payment method
 * @property int		 $zasilkovna_branch	 Zasilkovna delivery network branch ID
 * @property int		 $zasilkovna_id		 Zasilkovna delivery network order ID
 * @property-read string $status_change_date Date when the status changed last
 * @property-read int	 $price_items
 * @property-read int	 $price_delivery
 * @property-read int	 $price_payment
 * @property-read int	 $price_total
 * @property-read int	 $vat_rate
 * @property-read string $submission_date
 * @property-read int    $public_id
 */
class Order {

	const STATUS_NEW = 1;
	const STATUS_WAITING = 2;
	const STATUS_CONFIRMED = 3;
	const STATUS_SENT = 4;
	const STATUS_CANCELLED = 5;
	const STATUS_RETURNED = 6;
	const STATUS_CART = 7;

	const NOTIFY_TYPE_NEW = 1;
	const ZASILKOVNA_MAX_VALUE = 20000;
	const POSTAGE_DIS_THRESHOLD = 2999;

	/**
	 * @var \PunchyRascal\DonkeyCms\Application
	 */
	private $app;

	public $id;

	private $data = [];

	private static $instances;

	/**
	 * @var \PunchyRascal\DonkeyCms\Database\Database
	 */
	private $db;

	/**
	 * List of Order properties correspondinf to DB
	 * false = read only
	 * true = writable
	 * @var array
	 */
	private $properties = [
		'id' => false,
		'date' => false,
		'name' => true,
		'street' => true,
		'town' => true,
		'zip' => true,
		'email' => true,
		'phone' => true,
		'note' => true,
		'order' => true,
		'status' => true,
		'status_note' => true,
		'delivery' => true,
		'payment' => true,
		'zasilkovna_branch' => true,
		'zasilkovna_id' => true,
		'status_change_date' => false,
		'price_items' => false,
		'price_delivery' => false,
		'price_payment' => false,
		'price_total' => false,
		'vat_rate' => true,
		'submission_date' => false,
		'public_id' => false,
	];

	private $changedProperties = array();
	private $paymentMethods;
	private $deliveryMethods;

	/**
	 * Order items
	 * @var array
	 */
	private $items;

	/**
	 * @param int $id
	 * @return Order
	 */
	public static function getInstace(\PunchyRascal\DonkeyCms\Database\Database $db, \PunchyRascal\DonkeyCms\Application $app, $id) {
		if (!isset(self::$instances[$id])) {
			self::$instances[$id] = new self($db, $app, $id);
		}
		return self::$instances[$id];
	}

	/**
	 * @param int $id
	 * @return Order
	 */
	public static function getInstaceByPublicId(\PunchyRascal\DonkeyCms\Database\Database $db, \PunchyRascal\DonkeyCms\Application $app, $public_id) {
		$id = $db->getColumn("SELECT id FROM e_order WHERE public_id = %s", $public_id);
		return self::getInstace($db, $app, $id);
	}

	public function __construct(\PunchyRascal\DonkeyCms\Database\Database $db, \PunchyRascal\DonkeyCms\Application $app, $id = null) {
		$this->db = $db;
		$this->id = $id;
		$this->app = $app;
		if (!$this->id) {
			$this->db->query(
				"INSERT INTO `e_order` SET `date` = NOW(), status = %s, vat_rate = %s",
				self::STATUS_CART,
				Application::VAT_RATE
			);
			$this->id = $this->db->getInsertId();
		}
	}

	public function __isset($key) {
		return array_key_exists($key, $this->properties);
	}

	public function __get($key) {
		if (!array_key_exists($key, $this->properties)) {
			throw new Exception\Order("Cannot read property '$key' because it does not exist");
		}
		if ($this->id AND !$this->data) {
			$this->fetchData();
		}
		return array_key_exists($key, $this->data) ? $this->data[$key] : null;
	}

	public function __set($key, $value) {
		if (!array_key_exists($key, $this->properties)) {
			throw new Exception\Order("Cannot set property \"$key\" because it does not exist");
		}
		if ($this->properties[$key] === false) {
			throw new Exception\Order("Cannot set property \"$key\" because it is read-only");
		}
		$this->setDataItem($key, $value);
	}

	private function setDataItem($key, $value) {
		if ($this->$key !== $value) {
			$this->changedProperties[$key] = true;
		}
		$this->data[$key] = $value;
		$this->setDbColumn($key, $value);
	}

	private function setDbColumn($name, $value) {
		$this->db->query(
			"UPDATE e_order SET %s = %s WHERE id = %s",
			$this->db->createParam(Database::PARAM_IDENTIFIER, $name),
			$value,
			$this->id
		);
	}

	private function updatePrices() {
		$this->setDataItem('price_items', $this->getItemsTotal());
		$this->setDataItem('price_delivery', $this->getPostagePrice());
		$this->setDataItem('price_payment', $this->getPaymentPrice());
		$this->setDataItem('price_total', $this->getGrandTotal());
		return $this;
	}

	/**
	 * Receive this order's data from ouside for better performance
	 * when creating large amounts of instances
	 * @param array $data Row from order database table
	 * @throws Exception\Order
	 */
	public function setData(array $data) {
		if ($data['id'] !== $this->id) {
			throw new Exception\Order("Invalid data to be set. Own ID: '$this->id' Data ID: '$data[id]'");
		}
		$this->data = $data;
		return $this;
	}

	private function fetchData() {
		$this->data = $this->db->getRow("SELECT * FROM e_order WHERE id = %s", $this->id);
	}

	public function getAvailableDeliveryMethods() {
		if (!$this->deliveryMethods) {
			$this->loadDeliveryMethods();
		}
		$methods = $this->deliveryMethods;
		if ($this->getItemsTotal() > self::ZASILKOVNA_MAX_VALUE) {
			unset($methods['zasilkovna']);
		}
		return $this->deliveryMethods;
	}

	private function loadDeliveryMethods() {
		$this->deliveryMethods = $this->db
			->selectIndexed("SELECT * FROM e_delivery_method")
			->indexBy('id');
	}

	public function getPostagePrice() {
		if ($this->getItemsTotal() > self::POSTAGE_DIS_THRESHOLD) {
			return 0;
		}
		if (!$this->delivery) {
			return null;
		}
		if (!$this->deliveryMethods) {
			$this->loadDeliveryMethods();
		}
		if (!isset($this->deliveryMethods[$this->delivery])) {
			return null;
		}
		return $this->deliveryMethods[$this->delivery]['price'];
	}

	public function getPostageLabel() {
		if (!$this->delivery) {
			return null;
		}
		if (!$this->deliveryMethods) {
			$this->loadDeliveryMethods();
		}
		if (!isset($this->deliveryMethods[$this->delivery])) {
			return null;
		}
		return $this->deliveryMethods[$this->delivery]['name'];
	}

	public function getPaymentPrice() {
		if ($this->getItemsTotal() > self::POSTAGE_DIS_THRESHOLD) {
			return 0;
		}
		if (!$this->payment) {
			return null;
		}
		if (!$this->paymentMethods) {
			$this->loadPaymentMethods();
		}
		if (!isset($this->paymentMethods[$this->payment])) {
			return null;
		}
		return $this->paymentMethods[$this->payment]['price'];
	}

	private function loadPaymentMethods() {
		$this->paymentMethods = $this->db
			->selectIndexed("SELECT * FROM e_payment_method")
			->indexBy('id');
	}

	public function getPaymentLabel() {
		if (!$this->payment) {
			return null;
		}
		if (!$this->paymentMethods) {
			$this->loadPaymentMethods();
		}
		if (!isset($this->paymentMethods[$this->payment])) {
			return null;
		}
		return $this->paymentMethods[$this->payment]['name'];
	}

	private function getPaymentTimeType() {
		if (!$this->payment) {
			return null;
		}
		if (!$this->paymentMethods) {
			$this->loadPaymentMethods();
		}
		if (!isset($this->paymentMethods[$this->payment])) {
			return null;
		}
		return $this->paymentMethods[$this->payment]['type'];
	}

	public function getItemsTotal() {
		return $this->db->getColumn(
			"SELECT SUM(amount * price) FROM e_order_item WHERE order_id = %s",
			$this->id
		) ?? 0;
	}

	public function getGrandTotal() {
		return $this->getItemsTotal() + $this->getPaymentPrice() + $this->getPostagePrice();
	}

	public function getItems() {
		if ($this->items === null) {
			$this->items = $this->db->query("SELECT
				item.*,
				item.price * item.amount / (100 + ord.vat_rate) * ord.vat_rate AS vat_amount
				FROM e_order_item AS item
				INNER JOIN e_order AS ord ON ord.id = item.order_id
				WHERE order_id = %s", $this->id);
		}
		return $this->items;
	}

	public function getStatusLabel() {
		switch ($this->status) {
			case self::STATUS_NEW:		 return 'Nová';
			case self::STATUS_WAITING:	 return 'Čeká se';
			case self::STATUS_CONFIRMED: return 'Potvrzeno';
			case self::STATUS_SENT:		 return 'Odesláno';
			case self::STATUS_CANCELLED: return 'Zrušeno';
			case self::STATUS_RETURNED:  return 'Vráceno';
			case self::STATUS_CART:		 return 'Rozpracovaný košík';
		}
	}

	private function addItem($productId, $amount, $variant) {
		/**
		 * Mysql considers 2 NULLs as distinct values, so we have to check manually
		 */
		$itemId = $this->db->getColumn(
			'SELECT id FROM e_order_item WHERE
				order_id = %s
				AND product_id = %s
				AND (
					(%3$s IS NULL AND variant IS NULL)
					OR (%3$s IS NOT NULL AND variant = %3$s)
				)',
			$this->id,
			$productId,
			$variant
		);

		if ($itemId) {
			$this->db->query(
				"UPDATE e_order_item SET amount = amount + %d WHERE id = %s",
				$amount,
				$itemId
			);
		} else {
			$this->db->query(
				"INSERT INTO e_order_item SET
						order_id = %s,
						product_id = %s,
						product_name = (SELECT name FROM e_item WHERE id = %s),
						amount = %s,
						price = (SELECT price - discount FROM e_item WHERE id = %s),
						variant = %s",
				$this->id,
				$productId,
				$productId,
				$amount,
				$productId,
				$variant
			);
		}

		$this->items = null;
		$this->updatePrices();
	}

	public function sendEmailNotifications($type) {
		switch ($type) {
			case self::NOTIFY_TYPE_NEW:
				$body = $this->getNewOrderEmailBody();
				$mailClient = new EmailMessage;
				$mailClient->setFrom($this->app->config->emailFrom)
					->setTo($this->email)
					->setSubject('Potvrzení objednávky z ' . $this->app->config->appName)
					->setBodyHtml($body)
					->send();

				/**
				 * @todo use templates for emails
				 */
				$mailAdmin = new EmailMessage;
				$mailAdmin->setFrom($this->email)
					->setTo($this->app->config->emailFrom)
					->setSubject('Nová objednávka z ' . $this->app->config->appName)
					->setBodyHtml("<h2>Nová objednávka!</h2>
						<p><a href='".$this->app->config->appUrl."/?p=admin&action=e_orders'>Přejít k objednávkám</a></p>
						$body")
					->send();
				break;
		}
	}

	private function getNewOrderEmailBody() {
		$template = new \PunchyRascal\DonkeyCms\Template();
		return $template->setFileName('email/newOrder.twig')
			->setValue('model', $this)
			->process();
	}

	private function notifyZasilkovna() {
		if (!$this->app->useProductionFeatures() OR !$this->app->config->zasilkovna->key) {
			return;
		}
		if ($this->zasilkovna_branch) {
			$cod_price = $this->getPaymentTimeType() === 'on_delivery' ? $this->getGrandTotal() : 0;
			$soap = new \SoapClient("http://www.zasilkovna.cz/api/soap-php-bugfix.wsdl");
			$nameExploded = explode(" ", trim($this->name));
			try {
				$packet = (array) $soap->createPacket($this->app->config->zasilkovna->key, array(
					'number' => (string) $this->id,
					'name' => isset($nameExploded[0]) ? $nameExploded[0] : $this->name,
					'surname' => isset($nameExploded[1]) ? $nameExploded[1] : $this->name,
					'email' => $this->email,
					'phone' => (string) $this->phone,
					'addressId' => (string) $this->zasilkovna_branch,
					'cod' => (string) $cod_price,
					'value' => (string) $this->getGrandTotal(),
					'eshop' => $this->app->config->zasilkovna->eshop
				));
				$this->zasilkovna_id = $packet['id'];
			} catch(\SoapFault $e) {
				$this->app->logRecoverableError($e);
			}
		}
	}

	private function notifyHeureka() {
		if (!$this->app->useProductionFeatures() OR !$this->app->config->heurekaKey) {
			return;
		}

		try {
			$apiKey = $this->app->config->heurekaKey;
			$options = ['service' => \Heureka\ShopCertification::HEUREKA_CZ];
			$shopCertification = new \Heureka\ShopCertification($apiKey, $options);
			$shopCertification->setEmail($this->email);
			$shopCertification->setOrderId($this->id);
			foreach ($this->getItems() AS $item) {
				$shopCertification->addProductItemId($item['product_id']);
			}
			$shopCertification->logOrder();
		} catch (\Heureka\ShopCertification\Exception $e) {
			$log = new \PunchyRascal\DonkeyCms\Logger('heureka.log');
			$log->error("$e");
		}
	}

	public function place() {
		$this->updatePrices();
		$this->status = self::STATUS_NEW;
		$this->fetchData();
		$this->sendEmailNotifications(self::NOTIFY_TYPE_NEW);
		$this->notifyZasilkovna();
		$this->notifyHeureka();
	}

	public function getVatSum() {
		$vatSum = $this->db->getColumn(
			"SELECT SUM(
					(price * amount) / (100 + ord.vat_rate) * ord.vat_rate
				)
				FROM e_order_item AS item
				INNER JOIN e_order  AS ord ON ord.id = item.order_id
				WHERE order_id = %s",
			$this->id
		);
		$vatSum += $this->getPaymentPrice() / (100 + $this->vat_rate) * $this->vat_rate;
		$vatSum += $this->getPostagePrice() / (100 + $this->vat_rate) * $this->vat_rate;

		return $vatSum;
	}

	public function setItemAmount($itemId, $amount) {
		$this->db->query(
			"UPDATE e_order_item SET amount = %s WHERE id = %s AND order_id = %s",
			(int) $amount,
			(int) $itemId,
			$this->id
		);
		$this->items = null;
		$this->updatePrices();
	}

	public function removeItem($itemId) {
		$this->db->query(
			"DELETE FROM e_order_item WHERE id = %s AND order_id = %s",
			$itemId,
			$this->id
		);
		$this->items = null;
		$this->updatePrices();
	}

	public function addItemByProductId($productId, $variant = null, $amount = 1) {
		$product = $this->db->getRow(
			"SELECT * FROM e_item WHERE id = %d",
			(int) $productId
		);
		if (!$product['id']) {
			throw new ProductDoesNotExistException($productId);
		}
		$this->addItem($product['id'], $amount, $variant);
	}

}
