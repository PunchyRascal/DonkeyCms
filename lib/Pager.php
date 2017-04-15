<?php

namespace PunchyRascal\DonkeyCms;

use PunchyRascal\DonkeyCms\Http;
use PunchyRascal\DonkeyCms\UrlGenerator;

class Pager implements \ArrayAccess {

	const PAGE_LINKS_LIMIT = 10;

	/**
	 * Number of records shown per page
	 * @var int
	 */
	private $perPage;

	/**
	 * Number of all records
	 * @var int
	 */
	private $itemsCount;

	/**
	 * Optional additional params appended to page URLs
	 * @var array
	 */
	private $urlParams;

	/**
	 * Holds data used by the paging view
	 * @var array
	 */
	private $output;

	/**
	 * Current offset e.g. current page
	 * @var int
	 * @todo Pass this in contructor
	 */
	private $from;

	public function __construct($perPage, $itemsCount, array $urlParams = null) {
		$this->perPage = $perPage;
		$this->itemsCount = $itemsCount;
		$this->urlParams = $urlParams;
		$this->from = (int) Http::getGet('from');
		$this->prepareOutput();
	}

	private function prepareUrl() {
		$url = new UrlGenerator;
		$url
			->setPath('/')
			->setQueryParam('p', Http::getGet('p'))
			->setQueryParams($this->urlParams);
		return $url;
	}

	public function prepareOutput() {
		if ($this->itemsCount <= $this->perPage) {
			return;
		}

		$this->output = [
			'previous' => null,
			'next' => null,
			'numbers' => null
		];

		if ($this->from - $this->perPage >= 0) {
			$this->output['previous'] = $this->prepareUrl();
			if ($this->from - $this->perPage > 0) {
				$this->output['previous']->setQueryParam('from', $this->from - $this->perPage);
			}
		}

		$this->prepareNumberedPages();

		if ($this->from + $this->perPage <= $this->itemsCount) {
			$this->output['next'] = $this->prepareUrl()->setQueryParam('from', $this->from + $this->perPage);
		}
	}

	private function prepareNumberedPages() {
		$currentFrom = 0;
		$index = 0;
		while ($currentFrom < $this->itemsCount) {
			if ($currentFrom / $this->perPage >= self::PAGE_LINKS_LIMIT) {
				$this->output['numbers'][$index] = ['isEtc' => true];
				break;
			}

			$this->output['numbers'][$index] = ['url' => $this->prepareUrl()];
			if ($this->from === $currentFrom) {
				$this->output['numbers'][$index]['isActive'] = true;
			}
			if ($currentFrom > 0) {
				$this->output['numbers'][$index]['url']->setQueryParam('from', $currentFrom);
			}
			$this->output['numbers'][$index]['label'] = $currentFrom / $this->perPage + 1;
			$currentFrom += $this->perPage;
			$index++;
		}
	}

	public function offsetExists($offset) {
		return isset($this->output[$offset]);
	}

	public function offsetGet($offset) {
		return $this->output[$offset];
	}

	public function offsetSet($offset, $value) {

	}

	public function offsetUnset($offset) {

	}
}
