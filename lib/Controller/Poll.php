<?php

namespace PunchyRascal\DonkeyCms\Controller;

use PunchyRascal\DonkeyCms\Http;

class Poll extends Base {

	public function execute() {
		if (stripos(Http::getServer('HTTP_USER_AGENT'), 'bot') !== false) {
			Http::redirect(Http::getServer('HTTP_REFERER', Http::getServer('HTTP_HOST')));
		}

		$pollId = (int) Http::getGet('poll');
		$vote = (int) Http::getGet('vote');

		if ($pollId AND $vote <= 5 AND $vote >= 1 AND !Http::getCookie("pollVoted$pollId")) {
			$col = $this->db->createParam('ID', "opt$vote");
			$this->db->query(
				'UPDATE e_poll SET %1$s = IFNULL(%1$s, 0) + 1 WHERE id = %2$s',
				$col,
				$pollId
			);
			Http::setCookie("pollVoted$pollId", 1);
		}
		Http::redirect(Http::getServer('HTTP_REFERER', Http::getServer('HTTP_HOST')));
	}

}
