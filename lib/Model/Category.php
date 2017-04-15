<?php

namespace PunchyRascal\DonkeyCms\Model;

use PunchyRascal\DonkeyCms\Cache;
use PunchyRascal\DonkeyCms\Database\Database;

class Category {

	/**
	 * @todo Only used in one place
	 * @param \PunchyRascal\DonkeyCms\Database\Database $db
	 * @param int $parent
	 * @return array
	 */
	public static function getCategoriesForParent(Cache $cache, Database $db, $parent) {
		return unserialize(
			$cache->getByParams('categoriesForParent', [$parent], function () use($db, $parent) {
				$rows = $db->query(
					'SELECT cat.name, cat.id, cat.image_url
					FROM e_cat AS cat
					WHERE
						(
							(%1$s IS NOT NULL AND cat.parent_cat = %1$s)
							OR
							(%1$s IS NULL AND cat.parent_cat IS NULL)
						)
						AND cat.active = 1
					ORDER BY cat.sequence, cat.name',
					$parent
				);

				return serialize($rows);
			})
		);
	}

	/**
	 * @todo Avoid HTML in controller
	 * @param \PunchyRascal\DonkeyCms\Database\Database $db
	 * @param \stdClass $config
	 * @return string
	 */
	private static function printCategoryTree (Database $db, $config) {
		$catCond = $config->activeOnly ? 'AND cat.active = 1' : '';
		$return = "<ul>";
		/**
		 * @todo use query builder
		 */
		$cats = $db->query(
			'SELECT cat.*
			FROM e_cat AS cat
			WHERE
				(
					(%1$s IS NOT NULL AND cat.parent_cat = %1$s)
					OR
					(%1$s IS NULL AND cat.parent_cat IS NULL)
				)
				'.$catCond.'
			ORDER BY cat.sequence, cat.name',
			$config->activeCat
		);
		foreach ($cats AS $cat) {
			$return .= "
				<li class='" . ($cat['id'] == $config->activeCat ? 'active' : 'inactive') ."  " . ($cat['children'] ? 'hasSubs' : 'noSubs') ."'>
					<span data-children='$cat[children]' data-page='$config->page' data-id='$cat[id]' id=catToggleId$cat[id] class='toggle " . ($cat['children'] ? 'hasSubs\' title=Rozbalit' : 'noSubs\'') ."></span>
					<a class='catLabel' href='/?p=$config->page&amp;cat=$cat[id]'>$cat[name]</a> " . sprintf($config->lineContent, $cat['id'], $cat['name']);
			$return .= "</li>";
		}
		$return .= "</ul>";
		return $return;
	}

	/**
	 * Return tree of eshop categories
	 * @param array $config = array(
	 *		page => 'e-shop',
	 *		lineContent => '',
	 *		activeCat => 0,
	 *		activeOnly => true
	 * )
	 * @return string
	 */
	public static function getCategoryTree(Cache $cache, Database $db, $initial_config) {
		$defaults = [
			'page' => 'e-shop',
			'lineContent' => '',
			'activeCat' => null,
			'activeOnly' => true
		];
		$config = (object) array_merge($defaults, $initial_config);

		return $cache->getByParams(
			'categoryTree',
			(array) $config,
			function () use($db, $config) {
				return self::printCategoryTree($db, $config);
			}
		);
	}

}
