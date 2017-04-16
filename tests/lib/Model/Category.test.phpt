<?php

require __DIR__ . '/../../bootstrap.php';

use Tester\Assert;
use PunchyRascal\DonkeyCms\Model\Category;

class CategoryModelTest extends Tester\TestCase {

	/**
	 * @var Mockery\Mock
	 */
	private $db, $cache, $app, $config;

	protected function setUp() {
		Mockery::getConfiguration()->allowMockingNonExistentMethods(false);
		$this->db = Mockery::mock('PunchyRascal\DonkeyCms\Database\Database');
		$this->app = Mockery::mock('PunchyRascal\DonkeyCms\Application');
		$this->cache = Mockery::mock('PunchyRascal\DonkeyCms\Cache', [$this->app])->makePartial();
		$this->app->config = new stdClass();
		$this->app->config->useCache = true;
	}

	protected function tearDown() {
		Mockery::close();
	}

	public function testGetCategoriesForParent() {
		$this->cache->emptyCache('categoriesForParent');

		$this->db->shouldReceive('query')->andReturn('query-result')
			->once(); # result is cached

		Assert::same(
			'query-result',
			Category::getCategoriesForParent($this->cache, $this->db, 74)
		);
		Assert::same(
			'query-result',
			Category::getCategoriesForParent($this->cache, $this->db, 74)
		);
	}

	public function testGetCategoryTree() {
		$this->cache->emptyCache('categoryTree');
		$this->db->shouldReceive('query')->andReturnUsing(function ($query) {
			Assert::isMatching('/cat\.active = 1/', $query);
			return [
				['name' => 'Foo bar', 'id' => 7, 'children' => '']
			];
		})->once();
		Assert::isMatching(
			'#<ul>.*Foo bar.*</ul>#',
			Category::getCategoryTree($this->cache, $this->db, ['foo' => 'boo', 'activeOnly' => true])
		);
	}

}

$testCase = new CategoryModelTest;
$testCase->run();
