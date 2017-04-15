<?php

namespace PunchyRascal\DonkeyCms\Controller;

use PunchyRascal\DonkeyCms\Http;
use PunchyRascal\DonkeyCms\Session;

abstract class Base extends \PunchyRascal\DonkeyCms\StrictObject {

	/**
	 * @var \PunchyRascal\DonkeyCms\Template
	 */
	private $template;

	/**
	 * @var \PunchyRascal\DonkeyCms\Application
	 */
	protected $app;

	/**
	 * @var \PunchyRascal\DonkeyCms\Database\Database
	 */
	protected $db;

	/**
	 * @var \PunchyRascal\DonkeyCms\Logger
	 */
	public $log;

	/**
	 * @var \PunchyRascal\DonkeyCms\Cache
	 */
	protected $cache;

	public function __construct(\PunchyRascal\DonkeyCms\Application $app) {
		$this->app = $app;
		$this->db = $this->app->db;
		$this->log = $this->app->getLog();
		$this->cache = $this->app->getCache();
	}

	public function execute() {

	}

	/**
	 * @return \PunchyRascal\DonkeyCms\Template
	 */
	public function getTemplate() {
		if (!$this->template) {
			$this->template = new \PunchyRascal\DonkeyCms\Template();
			$this->setGlobals();
		}
		return $this->template;
	}

	private function setGlobals() {
		$this->template
			->setGlobalValue('appName', $this->app->config->appName)
			->setGlobalValue('appUrl', $this->app->config->appUrl)
			->setGlobalValue('appDescription', $this->app->config->appDescription)
			->setGlobalValue('googleAnalyticsId', $this->app->config->googleAnalyticsId)
			->setGlobalValue('facebookPageUrl', $this->app->config->facebookPageUrl)
			->setGlobalValue('emailTo', $this->app->config->emailTo)
			->setGlobalValue('showEditLink', Session::isAdminLogged())
			->setGlobalValue('globalMessage', $this->message())
			->setGlobalValue('vatRate', \PunchyRascal\DonkeyCms\Application::VAT_RATE)
			->setGlobalValue('eSearch', Http::getGet('eSearch'))
			->setGlobalValue('pageId', $this->getPageId())
			->setGlobalValue('polls', \PunchyRascal\DonkeyCms\Model\Poll::getTemplateData($this->db))
			->setGlobalValue('currentCatId', Http::getGet('cat'))
			->setGlobalValue('sideCategories', \PunchyRascal\DonkeyCms\Model\Category::getCategoryTree(
				$this->cache,
				$this->db,
				[
					'page' => Http::getGet('p') === 'admin' ? 'admin&amp;action=e_items' : 'e-shop',
					'activeOnly' => Http::getGet('p') === 'admin' ? !Session::isAdminLogged() : true
				]
			))
			->setGlobalValue('recaptchaSiteKey', $this->app->config->recaptcha->siteKey);
	}

	private function getPageId() {
		if (Http::getGet('p') === 'page') {
			return 'page' . ucfirst(Http::getGet('url'));
		}
		return Http::getGet('p');
	}

	public function output() {
		return $this->getTemplate()->process();
	}

	/**
	 * @todo move messages somewhere else?
	 */
	public function message() {
		switch( (int) Http::getGet('m')) {
			case 1: return ['type' => 'success', 'text' => "Operace byla úspěšně provedena."];
			case 5: return ['type' => 'success', 'text' => "Obrázky byly nahrány."];
			case 12: return ['type' => 'warning', 'text' => "Vyplňte prosím všechny povinné položky."];
			case 13: return ['type' => 'warning', 'text' => "Formulář obsahuje chyby"];
			case 15: return ['type' => 'success', 'text' => 'Zpráva byla odeslána. Budeme reagovat co nejdříve.'];
			case 16: return ['type' => 'warning', 'text' => 'Zprávu se nepodařilo odeslat. Omlováme se - zkuste nás prosím kontaktovat přímo - viz sekce kontakt'];
			case 17: return ['type' => 'success', 'text' => 'Komentář byl uložen'];
			case 18: return ['type' => 'warning', 'text' => 'Tato URL již existuje. Zvolte jinou.'];
			case 19: return ['type' => 'warning', 'text' => 'Některé obrázky se nahrály, jiné nikoliv'];
			case 20: return ['type' => 'warning', 'text' => 'Vyberte prosím pobočku Zásilkovny, kam chcete zboží dodat.'];
			case 21: return ['type' => 'warning', 'text' => 'Zvolte prosím způsob dopravy a platby.'];
			case 22: return ['type' => 'danger', 'text' => 'Tento produkt neexistuje.'];
			case 23: return ['type' => 'danger', 'text' => 'Účtenku se nepodařilo odeslat.'];
			case 24: return ['type' => 'danger', 'text' => 'Přihlášení se nepodařilo.'];
		}
	}

	public function isRequestMethod($method) {
		return $method === Http::getServer('REQUEST_METHOD');
	}

	/**
	 * To Satisfy trait requirement
	 * @return \PunchyRascal\DonkeyCms\Application
	 */
	protected function getApp() {
		return $this->app;
	}

	protected function dump() {
		$this->app->getDumper()->dump(...func_get_args());
	}

}
