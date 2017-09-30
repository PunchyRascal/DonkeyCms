<?php

namespace PunchyRascal\DonkeyCms\Controller\Service;

use PunchyRascal\DonkeyCms\Http;
use PunchyRascal\DonkeyCms\Cache;

/**
 * Resize/proxy product image on the fly
 */
class ProductImageResizer extends \PunchyRascal\DonkeyCms\Controller\Base {

	private $productId;
	private $newPath;
	private $maxWidth;
	private $doResize;

	public function execute() {
		$this->productId = Http::getGet('id');
		$this->maxWidth = Http::getGet('maxWidth');
		$this->doResize = !!$this->maxWidth;
	}

	public function output() {
		header("Content-type: image/jpeg");
		echo $this->cache->getByParams('resizedProductImage', [$this->productId, $this->maxWidth], function () {
			return $this->doResize ? $this->getResizedImage() : $this->getOriginalImage();
		}, 3600 * 24 * 30);
	}

	private function getOriginalImage() {
		return file_get_contents($this->getPath());
	}

	private function getPath() {
		$origPath = $this->db->getColumn("SELECT image_url FROM e_item WHERE id = %s", $this->productId);
		if (!$origPath) {
			$origPath = __DIR__ . "/../../../../../../public_html/upload/product/T/{$this->productId}_1.jpg";
		}
		return $origPath;
	}

	private function getResizedImage() {
		$origPath = $this->getPath();
		$this->newPath = __DIR__ . "/../../../../../../public_html/upload/thumbs/resizer_$this->productId.jpg";
		list($origW, $origH) = getimagesize($origPath);
		list($newW, $newH) = $this->calculateNewDimensions($origW, $origH);

		$orig = $this->createImageResource($origPath);
		$new = imagecreatetruecolor($newW, $newH);

		$bg = imagecolorallocate($new, 255, 255, 255);
		imagefill($new, 0, 0, $bg);

		imagecopyresampled($new, $orig, 0, 0, 0, 0, $newW, $newH, $origW, $origH);
		imagejpeg($new, $this->newPath, 95);

		$return = file_get_contents($this->newPath);
		unlink($this->newPath);
		return $return;
	}

	private function createImageResource($filename) {
		return imagecreatefromstring(file_get_contents($filename));
	}

	private function calculateNewDimensions($origW, $origH) {
		$div = $origW / $this->maxWidth;
		return [$this->maxWidth, $origH / $div];
	}

}
