<?php

namespace PunchyRascal\DonkeyCms\Controller\Admin;

use PunchyRascal\DonkeyCms\Http;
use PunchyRascal\DonkeyCms\Format;

class Uploader extends Base {

	private $uploadDir = "/../../../public_html/upload";
	private $folder;

	private function getFolderParam() {
		return Format::forFilename(Http::getGet('folder', Http::getPost('folder')));
	}

	private function getFolder() {
		$param = $this->getFolderParam();
		if ($param) {
			return 'custom_' . $param;
		}
		return null;
	}

	public function execute() {

		$this->folder = $this->getFolder();

		if (Http::getPost('createDir')) {
			$name = Format::forFilename(Http::getPost("dirName"));
			mkdir(__DIR__ . $this->uploadDir . "/custom_$name");
			mkdir(__DIR__ . $this->uploadDir . "/custom_$name/thumbs");
			Http::redirect('/?p=admin&action=uploader&m=1');
		}

		if (Http::getPost('filesSubmit')) {
			$failed = array();
			foreach (Http::getFile('files|size') AS $key => $size) {
				if ($size) {
					$originalName = Http::getFile("files|name|$key");
					$filename = __DIR__ . $this->uploadDir
						. ($this->folder ? "/$this->folder" : '')
						. "/" . Format::forFilename($originalName);
					$i = 2;
					$originalName = $filename;
					while (file_exists($filename)) {
						$filename = $this->getName($originalName) ."_$i.". $this->getExtension($originalName);
						$i++;
					}
					$result = move_uploaded_file(Http::getFile("files|tmp_name|$key"), $filename);
					if ($result) {
						$this->createThumbnail($filename);
					} else {
						$failed[] = Http::getFile('files|name');
					}
				}
			}
			if ($failed) {
				Http::redirect("/?p=admin&action=uploader&folder=".$this->getFolderParam() ."&m=19");
			}
			Http::redirect("/?p=admin&action=uploader&folder=".$this->getFolderParam() ."&m=1");
		}
	}

	public function output() {
		$this->getTemplate()->setFileName('admin/uploader.twig');
		$this->getTemplate()->setValue('files', $this->listFiles());
		$this->getTemplate()->setValue('folders', $this->listFolders());
		$this->getTemplate()->setValue('currentFolder', $this->getFolderParam());
		$this->getTemplate()->setValue('path', $this->folder ? "$this->folder/" : '');
		$this->getTemplate()->setValue('server', Http::getServer('SERVER_NAME'));
		return parent::output();
	}

	private function listFolders() {
		$dir = __DIR__ . $this->uploadDir;
		$handle = opendir($dir);
		$dirs = [];
		while ($file = readdir($handle)) {
			if (is_dir($dir.'/'.$file) AND substr($file, 0, 6) === 'custom') {
				$dirs[] = [
					'isDir' => true,
					'filename' => substr($file, 7),
				];
			}
		}
		return $dirs;
	}

	private function listFiles() {
		$dir = __DIR__ . $this->uploadDir . ($this->folder ? "/$this->folder" : '');
		$handle = opendir($dir);
		$files = [];
		while ($file = readdir($handle)) {
			if (!is_file($dir.'/'.$file) OR $file === '.gitignore') {
				continue;
			}

			$files[] = [
				'filename' => $file,
				'extension' => substr($file, -3, 3)
			];
		}
		return $files;
	}

	public function getExtension($filename) {
		return strtolower(preg_replace("/.*\.([^.]+)$/", "$1", $filename));
	}

	public function getName($filename) {
		return preg_replace("/(.*)\.([^.]+)$/", "$1", $filename);
	}

	private function createThumbnail($filename) {
		$ext = $this->getExtension($filename);

		if ($ext !== 'jpg' AND $ext !== 'png') {
			return;
		}

		list($width, $height) = getimagesize($filename);

		$newHeight = round($height / ($width / 200));

		$thumb = imagecreatetruecolor(200, $newHeight);
		imagefill($thumb, 0, 0, imagecolorallocate($thumb, 255, 255, 255));

		$originalImage = $this->createImgFromFile($ext, $filename);

		$thumbFilename = realpath(dirname($filename) . '/thumbs') .'/'. basename($filename);

		$count = 0;
		$check = false;
		while($check !== true) {
			if ($count > 10) {
				break;
			}
			$check = imagecopyresampled($thumb, $originalImage, 0, 0, 0, 0, 200, $newHeight, $width, $height);
			$this->outputImage($ext, $thumb, $thumbFilename);
			if ($check) {
				imagedestroy($thumb);
			}
			$count++;
		}

	}

	private function createImgFromFile($ext, $filename) {
		switch ($ext) {
			case 'jpg':
			case 'jpeg':
				return imagecreatefromjpeg($filename);
			case 'png':
				return imagecreatefrompng($filename);
		}
	}

	private function outputImage($ext, $thumb, $thumbFilename) {
		switch ($ext) {
			case 'jpg':
			case 'jpeg':
				imagejpeg($thumb, $thumbFilename, 95);
				break;
			case 'png':
				imagealphablending($thumb, false);
				imagesavealpha($thumb, true);
				imagepng($thumb, $thumbFilename);
				break;
		}
	}

}
