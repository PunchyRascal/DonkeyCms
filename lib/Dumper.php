<?php

namespace PunchyRascal\DonkeyCms;

/**
 * Dump arbitrary values for debugging purposes
 */
class Dumper {

	private $toDump = [];

	/**
	 * @var Application
	 */
	private $app;

	public function __construct (Application $app) {
		$this->app = $app;
	}

	public function __destruct() {
		if ($this->app->useProductionFeatures()) {
			return;
		}
		foreach ($this->toDump AS list($title, $var)) {
			echo '<div style="background: #fff; color: #000;">';
			echo "<h1>$title</h1>";
			echo "<pre>";
			var_dump($var);
			echo "</pre><hr>";
			echo '</div>';
		}

	}

	public function dump($var, $title = '*dump*') {
		$this->toDump[] = [$title, $var];
	}

}
