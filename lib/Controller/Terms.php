<?php

namespace PunchyRascal\DonkeyCms\Controller;

use PunchyRascal\DonkeyCms\Http;

class Terms extends Base {

	public function execute() {
		Http::redirect('/about/obchodni-podminky', Http::STATUS_MOVED_PERMANENTLY);
	}

}
