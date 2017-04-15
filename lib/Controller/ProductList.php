<?php

namespace PunchyRascal\DonkeyCms\Controller;

use PunchyRascal\DonkeyCms\Http;
use PunchyRascal\DonkeyCms\Session;
use PunchyRascal\DonkeyCms\Pager;
use PunchyRascal\DonkeyCms\Model\Category;

class ProductList extends Base {

	/**
	 * @var \PunchyRascal\DonkeyCms\Database\SelectQueryBuilder
	 */
	private $productsQuery;
	private $count, $categoryId, $showDiscounted;

	public function execute() {
		$this->categoryId = Http::getGet('cat');
		$this->showDiscounted = Http::getGet('sleva');
	}

	public function output() {
		return $this->cache->getByParams(
			'productList',
			$this->getAllParams(),
			function () {
				return $this->outputWithoutCache();
			}
		);
	}

	private function outputWithoutCache() {
		$cat = $this->getCategoryData();
		$products = $this->getProducts($cat);
		$this->getTemplate()->setFileName('productList.twig');
		$this->getTemplate()->data = array(
			'makes' => $this->getMakes(),
			'showCategoryHeading' => !!$this->categoryId,
			'category' => $cat,
			'pageTitle' => isset($cat['name']) ? $cat['name'] : '',
			'message' => Http::getGet('m'),
			'sortbyId' => Session::get('sortby'),
			'filter' => Http::getGet('filter'),
			'currentMake' => Http::getGet('make'),
			'showProducts' => $this->showProducts(),
			'searchMode' => !!Http::getGet('eSearch'),
			'showDiscounted' => $this->showDiscounted
		);

		$this->getTemplate()->setValue('subCategories', $this->getSubcategories());

		if ($this->showProducts()) {
			$this->getTemplate()->setValue('list', $products);
			$this->getTemplate()->setValue('count', $this->count);
			$this->getTemplate()->setValue('paging', new Pager(
				18, $this->count,
				[
					'cat' => $this->categoryId,
					'filter' => Http::getGet('filter'),
					'make' => Http::getGet('make'),
					'eSearch' => Http::getGet('eSearch'),
					'sleva' => $this->showDiscounted
				]
			));
		}

		return parent::output();
	}

	private function showProducts() {
		return $this->categoryId OR Http::getGet('eSearch') OR $this->showDiscounted;
	}

	private function getAllParams() {
		return [
			$this->categoryId,
			Http::getGet('from'),
			Http::getGet('eSearch'),
			Http::getGet('make'),
			Http::getGet('filter'),
			$this->getSortBy(),
			$this->showDiscounted
		];
	}

	private function getCategoryData() {
		return $this->db->getRow(
			"SELECT cat.*, parent.name AS parentName
			FROM e_cat AS cat
			LEFT JOIN e_cat AS parent ON parent.id = cat.parent_cat
			WHERE cat.id = %s LIMIT 1",
			$this->categoryId
		);
	}

	private function getMakes() {
		return $this->db->query(
			$this->productsQuery
				->copy()
				->calcFoundRows(false)
				->resetSelect()->select('DISTINCT(make)')
				->removeFlaggedJoin('imageJoin')
				->removeFlaggedWhere('makeCond')
				->resetGroupBy()
				->resetOrderBy()->orderBy('make ASC')
				->resetLimit()
		);
	}

	private function getSortBy() {
		$sortbyId = Http::getGet('sortby', Session::get('sortby', 1));
		Session::set('sortby', $sortbyId);
		return $sortbyId;
	}

	private function getProducts($cat) {
		$query = $this->db->createSelectQuery();
		$query->calcFoundRows();
		$query->select(
			'e_item.*, img.home_art AS imgExists, e_item.discount / (e_item.price / 100) AS discountPercent'
		);
		$query->from('e_item', 'e_item');
		$query->innerJoin('e_cat ON e_item.home = e_cat.id AND e_cat.active = 1');
		$query->leftJoin('item_image AS img ON img.home_art = e_item.id')->flagJoinAs('imageJoin');
		$query->where('e_item.active = 1 AND price > 0');

		if ($this->showDiscounted) {
			$query->where('AND e_item.discount > 0');
		}
		elseif (!$this->categoryId AND Http::getGet('eSearch')) {
			$query->where(
				'AND (
					e_item.name LIKE %1$s
					OR e_item.`desc` LIKE %1$s
					OR e_item.make LIKE %1$s
				)',
				$this->db->createParam('TEXT_FUZZY', trim(Http::getGet('eSearch')))
			);
		} else {
			$query->where(
				'AND home IN (%s)',
				$this->db->createParam(
					'INT_ARRAY',
					explode(',', $cat['children'].$this->categoryId)
				)
			);
		}

		if (Http::getGet('filter')) {
			$query->where('AND stock > 0');
		}

		if (Http::getGet('make')) {
			$query->where("AND make = %s", Http::getGet('make'))->flagWhereAs("makeCond");
		}

		switch ($this->getSortBy()) {
			default:
			case 1:
				$query->orderBy('e_item.name ASC');
				break;
			case 2:
				$query->orderBy('e_item.name DESC');
				break;
			case 3:
				$query->orderBy('e_item.price - e_item.discount ASC');
				break;
			case 4:
				$query->orderBy('e_item.price - e_item.discount DESC');
				break;
			case 5:
				$query->orderBy('e_item.make ASC');
				break;
			case 6:
				$query->orderBy('e_item.make DESC');
				break;
			case 7:
				$query->orderBy('e_item.discount / (e_item.price / 100) DESC');
				break;
			case 8:
				$query->orderBy('e_item.discount / (e_item.price / 100) ASC');
				break;
		}
		$query->groupBy('e_item.id')->limitFrom(Http::getGet('from', 0))->limitCount(18);
		$this->productsQuery = $query;
		$list = $this->db->query($query);
		$this->count = $this->db->foundRows();
		return $list;
	}

	public function getSubcategories() {

		if ($this->showDiscounted) {
			return null;
			return $this->db->query("
				SELECT DISTINCT(p.home), cat.name, cat.id, cat.image_url
				FROM e_item AS p
				INNER JOIN e_cat AS cat ON cat.id = p.home
				WHERE p.discount > 0 AND p.active = 1 AND cat.active = 1");
		}

		return Category::getCategoriesForParent(
			$this->cache,
			$this->db,
			$this->categoryId
		);
	}
}
