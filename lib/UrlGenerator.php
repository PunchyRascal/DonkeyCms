<?php

namespace PunchyRascal\DonkeyCms;

/**
 * Create and manipulate URLs
 */
class UrlGenerator {

	public $skipEmptyQueryParams = true;
	private $scheme;
	private $host;
	private $path;
	private $queryParams = [];

	public function __toString() {
		return $this->buildUrl();
	}

	public function setQueryParam($name, $value) {
		$this->queryParams[$name] = $value;
		return $this;
	}

	public function setQueryParams(array $params = null) {
		foreach ($params AS $name => $value) {
			$this->setQueryParam($name, $value);
		}
		return $this;
	}

	public function buildUrl() {
		return
			($this->scheme ? $this->scheme . '://' : '')
				. $this->host
				. $this->path
				. $this->buildQuery();
	}

    public function setPath($path) {
        $this->path = $path;
		return $this;
    }

    public function removeQueryParam($name) {
        unset($this->queryParams[$name]);
		return $this;
    }

	private function buildQuery() {
		if ($this->skipEmptyQueryParams) {
			foreach ($this->queryParams AS $key => $value) {
				if ($value === '') {
					unset($this->queryParams[$key]);
				}
			}
		}
		$query = http_build_query($this->queryParams);
		if ($query) {
			return '?' . $query;
		}
		return '';
	}

}