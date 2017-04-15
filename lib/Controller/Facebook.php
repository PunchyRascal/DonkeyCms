<?php

namespace PunchyRascal\DonkeyCms\Controller;

/**
 * Facebook
 */
class Facebook extends Base {

	public function output() {

		$conf = $this->app->config->facebook;
		$fb = new \Facebook\Facebook([
			'app_id' => $conf->appId,
			'app_secret' => $conf->appSecret,
			'default_graph_version' => 'v2.5',
			'default_access_token' => $conf->token,
		]);

		$stories = [];
		$posts = $fb->get("/$conf->pageId/posts?fields=comments,permalink_url,created_time,story,message,attachments&limit=10")->getDecodedBody();

		foreach ($posts['data'] AS $post) {

			if (!isset($post['message'])) {
				continue;
			}

			$photos = [];

			if (isset($post['attachments'])) {
				if (isset($post['attachments']['data'][0]['media']['image'])) {
					$photos[] = $post['attachments']['data'][0]['media']['image']['src'];
				}
				if (isset($post['attachments']['data'][0]['subattachments']['data'])) {
					foreach ($post['attachments']['data'][0]['subattachments']['data'] AS $att) {
						if ($att['media'] AND $att['media']['image']) {
							$photos[] = $att["media"]["image"]['src'];
						}
					}
				}
			}

			$comments = [];
			if (isset($post['comments'])) {
				$comments = $post['comments']["data"];
			}

			$stories[] = [
				'date' => $post['created_time'],
				'text' => $post['message'],
				'images' => $photos,
				'url' => $post['permalink_url'],
				'comments' => $comments,
			];
		}


		$this->getTemplate()->setFileName('facebook.twig')
			->setValue('stories', $stories);
		return parent::output();
	}

}