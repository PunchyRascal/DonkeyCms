<?php

namespace PunchyRascal\DonkeyCms\Controller\Admin;

use PunchyRascal\DonkeyCms\Model\Category;
use PunchyRascal\DonkeyCms\Http;

class CategoryEdit extends Base {

	public function execute() {
		if (Http::getPost('catEditSubmit')) {
			if (Http::getPost('id')) {
				$this->db->query(
					"UPDATE e_cat SET image_url = %s, active = %s, name = %s, "
						. "parent_cat = %s, sequence = %s WHERE id = %s",
					Http::getPost('image_url'),
					(int) Http::getPost('active'),
					Http::getPost('name'),
					Http::getPost('parent_cat') ?: null,
					(int) Http::getPost('sequence'),
					(int) Http::getPost('id')
				);
				$id = Http::getPost('id');
			}
			else {
				$this->db->query(
					"INSERT INTO e_cat SET image_url = %s, active = %s, "
						. "name = %s, parent_cat = %s, sequence = %s",
					Http::getPost('image_url'),
					(int) Http::getPost('active'),
					Http::getPost('name'),
					Http::getPost('parent_cat') ?: null,
					(int) Http::getPost('sequence')
				);
				$id = $this->db->getInsertId();
			}
			Http::redirect("/?p=admin&action=cat_edit&id=$id&m=1");
		}
	}

	public function output() {
		$this->getTemplate()->setFileName('admin/categoryEdit.twig');
		$lineContent = ' <a data-name="%2$s" data-id=%1$d class="btn btn-default btn-xs button">vybrat</a>';
		$this->getTemplate()->setValue('lineContent', $lineContent);
		if (Http::getGet('id')) {
			$this->getTemplate()->setValue('item', $this->db->getRow(
					"SELECT cat.*, parent.name AS parentName
					FROM e_cat AS cat
					LEFT JOIN e_cat AS parent ON parent.id = cat.parent_cat
					WHERE cat.id = %s",
					Http::getGet('id')
				)
			);
		}

		$this->getTemplate()->setValue('cats', Category::getCategoryTree(
				$this->cache,
				$this->db,
				['page' => 'admin', 'lineContent' => $lineContent]
			)
		);

		return parent::output();
	}

}
