<?php

namespace PunchyRascal\DonkeyCms;

use PunchyRascal\DonkeyCms\Format;

/**
 * Template parser
 */
class Template {

	private $templateFile;

	/**
	 * @var \Twig_Environment
	 */
	private static $engine;

	public $globalData = array();
	public $data = array();

	/**
	 * Set template file
	 * @param string $filename
	 * @return \PunchyRascal\DonkeyCms\Template
	 */
	public function setFileName($filename) {
		$this->templateFile = $filename;
		return $this;
	}

	public function setValue($key, $value) {
		if ($key == 'common') {
			throw new \InvalidArgumentException("key cannot be reserved word 'common'.");
		}
		$this->data[$key] = $value;
		return $this;
	}

	public function setGlobalValue($key, $value) {
		$this->globalData[$key] = $value;
		return $this;
	}

	public function appendArray($array) {
		$this->data = array_merge($this->data, $array);
		return $this;
	}

	public function setValues(array $templateData) {
		return $this->appendArray($templateData);
	}

	private function getEngine() {
		if (!self::$engine) {
			$loader = new \Twig_Loader_Filesystem(__DIR__ . '/../../../../templates');
			$twig = new \Twig_Environment($loader, [
				'cache' => __DIR__ . '/../cache/twig',
				'auto_reload' => true,
			]);
			$twig->getExtension('core')->setNumberFormat(0, ',', ' ');
			$twig->getExtension('core')->setDateFormat('j. n. Y', '%d days');
			self::$engine = $twig;
			$this->extendEngine();
		}
		return self::$engine;
	}

	private function extendEngine() {
		self::$engine->addFilter(new \Twig_SimpleFilter('entity_decode', function ($text) {
			return \html_entity_decode($text);
		}));
		self::$engine->addFilter(new \Twig_SimpleFilter('int', function ($text) {
			return intval($text);
		}));
		self::$engine->addFilter(new \Twig_SimpleFilter('markdown', function ($param) {
			$parser = new \Parsedown();
			return $parser->text($param);
		}));
		self::$engine->addFunction(new \Twig_SimpleFunction('cut', function ($text, $length) {
			return Format::shortenText($text, $length);
		}));
		self::$engine->addFilter(new \Twig_SimpleFilter('ucfirst', function ($text) {
			return ucfirst($text);
		}));
		self::$engine->addFilter(new \Twig_SimpleFilter('datetime', function ($date) {
			return date("j. n. Y H:i", strtotime($date));
		}));
		self::$engine->addFilter(new \Twig_SimpleFilter('br2nl', function ($text) {
			return strtr($text, ['<br>' => "\n", '<br/>' => "\n", '<br />' => "\n"]);
		}));
		self::$engine->addFilter(new \Twig_SimpleFilter('entity_decode', function ($text) {
			return html_entity_decode($text);
		}));
	}

	public function process() {
		if (!$this->templateFile) {
			throw new Exception("No templateFile set to parse");
		}
		return $this->getEngine()->render(
			$this->templateFile,
			array_merge(['common' => $this->globalData], $this->data)
		);
	}

}
