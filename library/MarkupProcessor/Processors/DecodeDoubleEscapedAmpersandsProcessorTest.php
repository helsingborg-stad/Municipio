<?php

namespace Municipio\MarkupProcessor\Processors;

use PHPUnit\Framework\TestCase;

class DecodeDoubleEscapedAmpersandsProcessorTest extends TestCase
{
    public function testDecodesRenderedLabelsWithoutChangingUrlsOrRawContent(): void
    {
        $input = '<a href="/?q=R&amp;amp;D" aria-label="R&amp;amp;D" title="R&amp;amp;D">R&amp;amp;D</a>'
            . '<img alt="A &amp;amp; B" src="/?q=A&amp;amp;B">'
            . '<!-- R&amp;amp;D -->'
            . '<script>const label = "R&amp;amp;D";</script>'
            . '<style>.x::before { content: "R&amp;amp;D" }</style>';

        $expected = '<a href="/?q=R&amp;amp;D" aria-label="R&amp;D" title="R&amp;D">R&amp;D</a>'
            . '<img alt="A &amp; B" src="/?q=A&amp;amp;B">'
            . '<!-- R&amp;amp;D -->'
            . '<script>const label = "R&amp;amp;D";</script>'
            . '<style>.x::before { content: "R&amp;amp;D" }</style>';

        $processor = new DecodeDoubleEscapedAmpersandsProcessor();

        $this->assertSame($expected, $processor->process($input));
        $this->assertSame($expected, $processor->process($expected));
    }
}
