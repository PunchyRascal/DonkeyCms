<?php

require __DIR__ . '/../../bootstrap.php';

use Tester\Assert;

class FrbsModelOrderUnitTest extends Tester\TestCase {

	const ORDER_ID = 654;

	public $object;
	private $db, $app;

	public function setUp() {
		Mockery::getConfiguration()->allowMockingNonExistentMethods(false);
		$this->db = Mockery::mock('PunchyRascal\DonkeyCms\Database\Database');
		$this->db->shouldReceive('query');
		$this->db->shouldReceive('createParam');
		$this->db->shouldReceive('getInsertId')->andReturn(self::ORDER_ID);
		$this->app = Mockery::mock('PunchyRascal\DonkeyCms\Application');
		$this->object = new PunchyRascal\DonkeyCms\Model\Order($this->db, $this->app);
	}

	protected function tearDown() {
		parent::tearDown();
		Mockery::close();
	}

	public function testNewOrder() {
		Assert::equal(self::ORDER_ID, $this->object->id);
	}

	public function testGetter() {
		$this->object = \PunchyRascal\DonkeyCms\Model\Order::getInstace($this->db, $this->app, 7);
		$this->db->shouldReceive('getRow')->andReturn(array('name' => "Emil Mock"));
		Assert::same(
			"Emil Mock",
			$this->object->name
		);
	}

	public function testItemsTotal() {
		$this->db->shouldReceive('getColumn')->with(
			"SELECT SUM(amount * price) FROM e_order_item WHERE order_id = %s",
			self::ORDER_ID
		)->andReturn(897);
		Assert::same(897, $this->object->getItemsTotal());
	}

	public function testProperties() {
		$this->db->shouldReceive('query');#->with()
		$this->object->setData(['id' => self::ORDER_ID, 'name' => "Emil"]);
		$this->object->name = "Emil";
		Assert::same('Emil', $this->object->name);
	}

	public function testSetData() {
		Assert::exception(
			function () {
				$this->object->setData(['id' => 9]);
			},
			'\Exception',
			"Invalid data to be set. Own ID: '".self::ORDER_ID."' Data ID: '9'"
		);
	}

	public function testInvalidProperty() {
		Assert::exception(
			function () {
				$this->object->sysel;
			},
			'\Exception',
			"Cannot read property 'sysel' because it does not exist"
		);
	}

}

$testCase = new FrbsModelOrderUnitTest();
$testCase->run();