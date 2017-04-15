<?php

namespace PunchyRascal\DonkeyCms\Model;

class CategoryTraversal {

	private $db;

	public function __construct(\PunchyRascal\DonkeyCms\Database\Database $db) {
		$this->db = $db;
	}

	private function traverse ($parent, $ancestors) {
		$children = $this->db->query("SELECT id FROM e_cat WHERE parent_cat = %s", $parent);
		if (count($children) === 0) {
			return;
		}
		foreach ($children AS $child) {
 			$this->db->query(
				"UPDATE e_cat SET children = CONCAT(children, %s, ',') WHERE id IN(%s)",
				$child['id'],
				$p = $this->db->createParam('IN', $ancestors)
 			);
			$newAncestors = $ancestors;
			$newAncestors[] = $child['id'];
			$this->traverse($child['id'], $newAncestors);
 		}
	}

	public function rebuildChildren () {
		$this->db->beginTransaction();

		$this->db->query("UPDATE e_cat SET children = ''");

		$cats = $this->db->query("SELECT id FROM e_cat WHERE parent_cat IS NULL");

		foreach ($cats AS $cat) {
			$this->traverse($cat['id'], array($cat['id']));
		}

		$this->db->commitTransaction();
	}

}
