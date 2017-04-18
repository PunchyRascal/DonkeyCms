<?php

namespace spec\PunchyRascal\DonkeyCms;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class FormatSpec extends ObjectBehavior
{

	public function it_escapes_text_for_html()
    {
        $this::forHtml('Foo<boo>bar')->shouldReturn("Foo&lt;boo&gt;bar");
    }

	public function it_shortens_text()
    {
        $this::shortenText('Foo b.aa dee', 6)->shouldReturn("Foo b&hellip;");
		$this::shortenText('Foo     ', 6)->shouldReturn("Foo");
		$this::shortenText('Foo     boo', 6)->shouldReturn("Foo&hellip;");
		$this::shortenText('Foo <a>boo</a>     boo', 7)->shouldReturn("Foo boo&hellip;");
		$this::shortenText('Foo €          boo', 7)->shouldReturn("Foo €&hellip;");
    }

}
