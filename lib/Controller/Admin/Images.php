<?php

namespace PunchyRascal\DonkeyCms\Controller\Admin;

use PunchyRascal\DonkeyCms\Http;

class Images extends Base {

	const MAX_DIMENSION = 240;

	private $referenceId;
	private $table;
	private $directory;
	private $path;
	private $setup;
	private $type;

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
		$this->setup = $this->db->getRow(
			"SELECT * FROM donkey_entity_image_setup WHERE id = %s",
			Http::getPost("type", Http::getGet("type"))
		);
		$this->referenceId = (int) Http::getPost('id', Http::getGet('id'));
		$this->table = $this->db->createParam("ID", $this->setup['image_table_name']);
		$this->directory = $this->setup['directory'];
		$this->type = $this->setup['id'];

		if (!$this->referenceId) {
			throw new \Exception("Missing reference ID");
		}

		if (!$this->setup) {
			throw new \Exception("Missing setup");
		}
	}

	public function output() {
		$this->getTemplate()->setFileName('admin/images.twig');

		$item = $this->db->getRow(
			"SELECT %s FROM %s WHERE id = %s",
			$this->db->createParam("ID", $this->setup['label_name']),
			$this->db->createParam("ID", $this->setup['table_name']),
			$this->referenceId
		);


		$this->getTemplate()->appendArray([
			'dirName' => $this->directory,
			'type' => $this->setup['id'],
			'item' => $item,
			'detailPageName' => $this->setup['detail_page_name'],
			'itemId' => $this->referenceId,
			'images' => $this->db->getRows(
				"SELECT * FROM %s WHERE home_art = %s ORDER BY art_img_count ASC",
				$this->table,
				$this->referenceId
			)
		]);

		return parent::output();
	}

	private function handleAdd() {
		if (Http::getFile('moreImages|size|0') OR Http::getFile('mainImage|size')) {
			$files = $this->getNormalizedFileList();

			foreach ($files AS $index => $file) {
				$this->handleFile($index, $file);
			}
			Http::redirect("/?p=admin&action=images&id=$this->referenceId&type=$this->type&m=5");
		}
		Http::redirect("/?p=admin&action=images&id=$this->referenceId&type=$this->type&m=13");
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
		if (!copy($file['tmp_name'], $imagePath)) {
			throw new \Exception("Could not copy uploaded file to $imagePath");
		}
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
		$index = Http::getPost('index');

		$this->db->query("DELETE FROM %s WHERE home_art = %s AND art_img_count = %s", $this->table, $this->referenceId, $index);
		unlink($this->path.$this->directory.'/'.$this->referenceId.'_'. $index .'.jpg');
		unlink($this->path.$this->directory.'/T/'. $this->referenceId .'_'. $index .'.jpg');

		Http::redirect("/?p=admin&action=images&type=$this->type&id=$this->referenceId&m=1");
	}

}
