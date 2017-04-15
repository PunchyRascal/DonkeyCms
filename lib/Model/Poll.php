<?php

namespace PunchyRascal\DonkeyCms\Model;

use PunchyRascal\DonkeyCms\Http;

class Poll {

	public static function getTemplateData(\PunchyRascal\DonkeyCms\Database\Database $db) {
		$polls = $db->query("SELECT * FROM e_poll WHERE active = 'on' ORDER BY date DESC");
		$pollsData = [];
		foreach ($polls AS $poll) {
			$id = $poll['id'];
			$allVotesCount = 0;
			for ($i = 1; $i <= 5; $i++) {
				$allVotesCount += $poll["opt$i"];
			}
			$onePercent = $allVotesCount / 100;

			if ($onePercent == 0) {
				$onePercent = 100;
			}
			$length = 190;
			$pollsData[$id] = [
				'question' => $poll['question'],
				'canVote' => !Http::getCookie("pollVoted$id"),
				'options' => [],
				'allVotesCount' => $allVotesCount,
			];

			for ($i = 1; $i <= 5; $i++) {
				if ($poll["ans$i"] != '') {
					$pollsData[$id]['options'][] = [
						'url' => "/?p=poll&vote=$i&poll=$poll[id]",
						'label' => $poll["ans$i"],
						'barWidth' => round($length / 100 * ($poll["opt$i"] / $onePercent)),
						'percent' => round($poll["opt$i"] / $onePercent)
					];
				}
			}
		}
		return $pollsData;
	}

}
