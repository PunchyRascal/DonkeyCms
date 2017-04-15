<?php

namespace PunchyRascal\DonkeyCms\Controller;

use PunchyRascal\DonkeyCms\Http;

class Returns extends Base {

	public function execute() {
		Http::redirect('/about/reklamacni-rad', Http::STATUS_MOVED_PERMANENTLY);
	}

}
