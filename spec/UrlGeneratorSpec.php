<?php

namespace spec\PunchyRascal\DonkeyCms;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class UrlGeneratorSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('PunchyRascal\DonkeyCms\UrlGenerator');
    }

	function it_sets_url_params()
    {
        $this->setQueryParam('foo', 'bar')->buildUrl()->shouldBe("?foo=bar");
    }

	function it_sets_multiple_params()
    {
        $this->setQueryParams(
			['foo' => 'bar', 'boo' => 'moo']
		)->buildUrl()->shouldBe("?foo=bar&boo=moo");
    }

	function it_combines_path_and_query()
    {
        $this
			->setPath('/')
			->setQueryParam('foo', 'bar')
			->buildUrl()
			->shouldBe("/?foo=bar");
    }

	function prepareUrl() {
		return $this
			->setPath('/')
			->setQueryParam('foo', 'bar')
			->setQueryParam('scooby', 'doo');
	}

	function it_removes_query_param()
    {
		$this->prepareUrl()
        	->buildUrl()
			->shouldBe("/?foo=bar&scooby=doo");

		$this->prepareUrl()
			->removeQueryParam('foo')
			->buildUrl()
			->shouldBe("/?scooby=doo");
    }

}
