<?php

namespace Municipio\MarkupProcessor\Processors;

use PHPUnit\Framework\TestCase;

class EscapeRawAmpersandsProcessorTest extends TestCase
{
    public function testEscapesTextWithoutChangingMarkupOrRawContent(): void
    {
        $input = '<p title="A &amp; B">A & B &amp; C &copy; D &bogus;</p>'
            . '<template><span>One & two</span></template>'
            . '<script>if (a && b) {}</script>'
            . '<style>.x::before { content: "A & B" }</style>';

        $expected = '<p title="A &amp; B">A &amp; B &amp; C &copy; D &amp;bogus;</p>'
            . '<template><span>One &amp; two</span></template>'
            . '<script>if (a && b) {}</script>'
            . '<style>.x::before { content: "A & B" }</style>';

        $processor = new EscapeRawAmpersandsProcessor();

        $this->assertSame($expected, $processor->process($input));
        $this->assertSame($expected, $processor->process($expected));
    }
}
