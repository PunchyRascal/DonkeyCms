<?php

namespace PunchyRascal\DonkeyCms\Controller\Admin;

use PunchyRascal\DonkeyCms\Model\Category;
use PunchyRascal\DonkeyCms\Http;

class CategoryList extends Base {

	public function execute() {
		if (Http::getGet('id') AND Http::getGet('delete')) {
			try {
				$this->db->query(
					"DELETE FROM e_cat WHERE id = %s LIMIT 1",
					Http::getGet('id')
				);
				Http::redirect("/?p=admin&action=e_cat&m=1");
			} catch (\PunchyRascal\DonkeyCms\Database\Exception\ForeignKey\ParentRowHasChildren $e) {
				$this->getTemplate()->setValue('existingRecordsError', true);
			}
		}
	}

	public function output() {
		$this->getTemplate()->setFileName('admin/categoryList.twig');

		/**
		 * @todo remove HTML from controller
		 */
		$lineContent = '<a href="/?p=admin&amp;action=cat_edit&amp;id=%1$d" class="btn btn-default btn-xs">upravit</a>
			<a onclick="CheckDelete(\'action=e_cat&amp;delete=1&amp;id=%1$d\')" class="btn btn-warning btn-xs">smazat</a>';

		$this->getTemplate()->setValue('lineContent', $lineContent);

		$this->getTemplate()->setValue(
			'cats',
			Category::getCategoryTree(
				$this->cache,
				$this->db,
				[
					'page' => 'admin',
					'lineContent' => $lineContent,
					'activeOnly' => false
				]
			)
		);

		return parent::output();
	}

}
