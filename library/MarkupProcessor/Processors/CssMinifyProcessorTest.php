<?php

namespace Municipio\MarkupProcessor\Processors;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class CssMinifyProcessorTest extends TestCase
{
    #[TestDox('minifies CSS content within <style> tags in the markup')]
    public function testProcess(): void
    {
        $processor = new CssMinifyProcessor();

        $input = '<style>/* Comment */ body { color: red; }</style>';
        $expectedOutput = '<style> body { color: red; }</style>';

        $this->assertEquals($expectedOutput, $processor->process($input));
    }
}
