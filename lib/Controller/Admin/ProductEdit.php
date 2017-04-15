<?php

namespace PunchyRascal\DonkeyCms\Controller\Admin;

use PunchyRascal\DonkeyCms\Http;

class ProductEdit extends Base {

	public $id;

	public function execute() {
		if (Http::getPost('e_item_edit_submit')) {
			if (Http::getPost('id')) {
				$this->edit();
			}
			else {
				$this->db->query(
					"INSERT INTO e_item SET
					`active` = %s,
					`name` = %s,
					`desc` = %s,
					`home` = %s,
					`stock` = %s,
					`price` = %s,
					`make` = %s,
					`discount` = %s,
					`variant` = %s,
					`availability` = %s,
					`ean` = %s,
					`availability_days` = %s,
					`featured` = %s",
					(int) Http::getPost('active'),
					Http::getPost('name'),
					Http::getPost('desc'),
					Http::getPost('home') ?: null,
					(int) Http::getPost('stock'),
					(int) Http::getPost('price'),
					Http::getPost('make'),
					(int) abs(Http::getPost('discount')),
					Http::getPost('variant'),
					Http::getPost('availability'),
					Http::getPost('ean'),
					(int) Http::getPost('availability_days'),
					(int) Http::getPost('featured')
				);
				$id = $this->db->getInsertId();
				Http::redirect("/?p=admin&action=e_item_edit&id=$id&m=1");
			}
		}
	}

	private function edit() {
		$this->db->query(
			"UPDATE `e_item` SET
				`active` = %s,
				`featured` = %s,
				`availability_days` = %s,
				`ean` = %s,
				`availability` = %s,
				`variant` = %s,
				`name` = %s,
				`home` = %s,
				`desc` = %s,
				`stock` = %s,
				`price` = %s,
				`make` = %s,
				`discount` = %s
			WHERE `id` = %s",
			(int) Http::getPost('active'),
			(int) Http::getPost('featured'),
			(int) Http::getPost('availability_days'),
			Http::getPost('ean'),
			Http::getPost('availability'),
			Http::getPost('variant'),
			Http::getPost('name'),
			Http::getPost('home') ?: null,
			Http::getPost('desc'),
			(int) Http::getPost('stock'),
			(int) Http::getPost('price'),
			Http::getPost('make'),
			(int) abs(Http::getPost('discount')),
			(int) Http::getPost('id')
		);
		$id = Http::getPost('id');
		if (Http::getPost('import_resolved')) {
			$this->db->query("UPDATE e_item SET import_resolved = 1 WHERE id = %d", (int) $id);
		}
		if (Http::getPost('auto_update')) {
			$this->db->query("UPDATE e_item SET auto_update = 1 WHERE id = %d", (int) $id);
		} else {
			$this->db->query("UPDATE e_item SET auto_update = 0 WHERE id = %d AND origin IS NOT NULL", (int) $id);
		}
		Http::redirect("/?p=admin&action=e_item_edit&id=$id&m=1");
	}

	public function output() {
		/**
		 * @todo avoid HTML in controller
		 */
		$lineContent = ' <a data-name="%2$s" data-id=%1$d class="button btn btn-default btn-xs">vybrat</a>';
		$this->getTemplate()->setValue('lineContent', $lineContent);
		$this->getTemplate()->setValue('catSelector', \PunchyRascal\DonkeyCms\Model\Category::getCategoryTree(
			$this->cache,
			$this->db,
			[
				'page' => 'admin',
				'lineContent' => $lineContent
			]
		));

		if (Http::getGet('id')) {
			$this->getTemplate()->setValue('item', $this->db->getRow(
				"SELECT item.*, cat.name AS catName, cat_parent.name AS catParentName
				FROM e_item AS item
				LEFT JOIN e_cat AS cat ON cat.id = item.home
				LEFT JOIN e_cat AS cat_parent ON cat_parent.id = cat.parent_cat
				WHERE item.id = %s",
				Http::getGet('id')
			));
		}

		$this->getTemplate()->setFileName('admin/productEdit.twig');
		return parent::output();
	}

}
