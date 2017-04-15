<?php

namespace PunchyRascal\DonkeyCms\Controller\Admin;

use PunchyRascal\DonkeyCms\Http;

class Images extends Base {

	const MAX_DIMENSION = 240;

	private $referenceId;
	private $table;
	private $directory;
	private $redirectUrl;
	private $path;

	public function execute() {
		$this->initData();
		if (Http::getPost('delete')) {
			$this->handleDelete();
		}
		if (Http::getPost('imagesSubmit')) {
			$this->handleAdd();
		}
	}

	private function initData() {
		$this->path = __DIR__ . '/../../../../../../public_html/';
		$this->referenceId = (int) Http::getPost('id', Http::getGet('id'));
		if (Http::getPost('type', Http::getGet('type')) == 'art') {
			$this->table = $this->db->createParam('ID', 'art_image');
			$this->directory = 'upload/article';
			$this->redirectUrl = 'action=images&type=art&id='. $this->referenceId;
		} else {
			$this->table = $this->db->createParam('ID', 'item_image');
			$this->directory = 'upload/product';
			$this->redirectUrl = 'action=images&type=product&id='. $this->referenceId;
		}
	}

	public function output() {
		$this->getTemplate()->setFileName('admin/images.twig');

		if ($this->directory === 'upload/product') {
			$item = $this->db->getRow("SELECT 'e-shop' AS p, name FROM e_item WHERE id = %s", $this->referenceId);
		} else {
			$item = $this->db->getRow("SELECT 'art' AS p, heading AS name FROM article WHERE id = %s", $this->referenceId);
		}

		$this->getTemplate()->appendArray(array(
			'dirName' => $this->directory,
			'type' => Http::getGet('type'),
			'item' => $item,
			'itemId' => $this->referenceId,
			'images' => $this->db->query(
				"SELECT * FROM %s WHERE home_art = %s ORDER BY art_img_count ASC", $this->table, $this->referenceId
			)
		));

		return parent::output();
	}

	private function handleAdd() {
		if (Http::getFile('moreImages|size|0') OR Http::getFile('mainImage|size')) {
			$files = $this->getNormalizedFileList();

			foreach ($files AS $index => $file) {
				$this->handleFile($index, $file);
			}
			Http::redirect("/?p=admin&$this->redirectUrl&m=5");
		}
		Http::redirect("/?p=admin&$this->redirectUrl&m=13");
	}

	private function handleFile($index, $file) {
		$currentImageNumber = $this->db->getColumn(
			"SELECT IFNULL(MAX(art_img_count), 0) FROM %s WHERE home_art = %s",
			$this->table,
			$this->referenceId
		);
		$extension = 'jpg';

		if ($index === 1) {
			$this->db->query("DELETE FROM %s WHERE home_art = %s AND art_img_count = 1", $this->table, $this->referenceId);
			$newImageNumber = 1;
		} else {
			$newImageNumber = $currentImageNumber > 0 ? $currentImageNumber + 1 : 2;
		}
		$this->db->query(
			"INSERT INTO %s VALUES(%s, %s, %s)",
			$this->table,
			$this->referenceId,
			$newImageNumber,
			$extension
		);
		$imagePath = $this->path.$this->directory ."/". $this->referenceId. "_". $newImageNumber. ".". $extension;
		copy($file['tmp_name'], $imagePath);
		// thumbnail
		$thumbPath = $this->path.$this->directory ."/T/". $this->referenceId. "_". $newImageNumber. ".". $extension;
		list($width, $height) = getimagesize($imagePath);
		list($thumbWidth, $thumbHeight) = $this->getThumbDimensions($width, $height);
		$thumbResource = imagecreatetruecolor($thumbWidth, $thumbHeight);
		$originalResource = imagecreatefromjpeg($imagePath);
		$thumbCreated = false;
		$count = 0;
		while ($thumbCreated !== true AND $count < 10) {
			$thumbCreated = imagecopyresampled($thumbResource, $originalResource, 0, 0, 0, 0, $thumbWidth, $thumbHeight, $width, $height);
			imagejpeg($thumbResource, $thumbPath, 95);
			$count++;
		}
		// watermark
		$watermark = imagecreatefrompng($this->path . "images/watermark.png");
		list($watermarkWidth, $watermarkHeight) = getimagesize("./images/watermark.png");
		imagecopy($originalResource, $watermark, 0, $height - $watermarkHeight, 0, 0, $watermarkWidth, $watermarkHeight);
		imagejpeg($originalResource, $imagePath, 95);
	}

	private function getThumbDimensions($originalWidth, $originalHeight) {
		if ($originalWidth > $originalHeight) {
			$xDimension = self::MAX_DIMENSION;
			$yDimension = $xDimension / ($originalWidth / $originalHeight);
		} else {
			$yDimension = self::MAX_DIMENSION;
			$xDimension = $yDimension / ($originalHeight / $originalWidth);
		}
		return [$xDimension, $yDimension];
	}

	private function getNormalizedFileList() {
		$files = array();
		$index = 1;
		if (Http::getFile('mainImage|size')) {
			$files[1] = Http::getFile('mainImage');
		}
		$index++;

		foreach (Http::getFile('moreImages|size') AS $key => $size) {
			if ($size AND Http::getFile("moreImages|type|$key") === 'image/jpeg') {
				$files[$index] = array(
					'name' => Http::getFile("moreImages|name|$key"),
					'type' => Http::getFile("moreImages|type|$key"),
					'tmp_name' => Http::getFile("moreImages|tmp_name|$key"),
					'error' => Http::getFile("moreImages|error|$key"),
					'size' => Http::getFile("moreImages|size|$key"),
				);
				$index++;
			}
		}
		return $files;
	}

	private function handleDelete() {
		$type = Http::getPost('type');
		$index = Http::getPost('index');

		$this->db->query("DELETE FROM %s WHERE home_art = %s AND art_img_count = %s", $this->table, $this->referenceId, $index);
		unlink($this->path.$this->directory.'/'.$this->referenceId.'_'. $index .'.jpg');
		unlink($this->path.$this->directory.'/T/'. $this->referenceId .'_'. $index .'.jpg');

		Http::redirect("/?p=admin&action=images&type=$type&id=$this->referenceId&m=1");
	}

}
