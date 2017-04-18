<?php

namespace spec\PunchyRascal\DonkeyCms;

use PhpSpec\ObjectBehavior;
use Tester\Assert;

class PagerSpec extends ObjectBehavior
{
    public function it_generates_pager_data()
    {
		$this->beConstructedWith(2, 4, ['foo' => 'bar']);
		$data = $this->getWrappedObject();

		Assert::true(!isset($data['previous']));
		Assert::equal('/?foo=bar&from=2', $data['next']."");
		Assert::equal(2, count($data['numbers']));

		$page1 = $data['numbers'][0];
		Assert::equal('/?foo=bar', $page1['url']."");
		Assert::equal('/?foo=bar', $page1['url']."");
		Assert::equal(1, $page1['label']);
		Assert::true($page1['isActive']);

		$page2 = $data['numbers'][1];
		Assert::equal('/?foo=bar&from=2', $page2['url']."");
		Assert::equal('/?foo=bar&from=2', $page2['url']."");
		Assert::equal(2, $page2['label']);
		Assert::true(!isset($page2['isActive']));
    }

	public function it_generates_nothing_if_not_needed()
    {
		$this->beConstructedWith(16, 4, ['foo' => 'bar']);
		$data = $this->getWrappedObject();
		Assert::false(isset($data['previous']));
		Assert::false(isset($data['next']));
		Assert::false(isset($data['numbers']));
    }
}
