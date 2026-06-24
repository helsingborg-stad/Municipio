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

        $input = <<<STYLE
            <style>
                /* This is a comment */
                body {
                    color: red;
                }
            </style>
            STYLE;

        $expectedOutput = '<style> body { color: red; }</style>';

        $this->assertEquals($expectedOutput, $processor->process($input));
    }

    #[TestDox('preserves attributes on minified style tags')]
    public function testProcessPreservesStyleAttributes(): void
    {
        $processor = new CssMinifyProcessor();

        $input = '<style id="wp-custom-css" data-test="custom-css">body { background: red; }</style>';
        $expectedOutput = '<style id="wp-custom-css" data-test="custom-css">body { background: red; }</style>';

        $this->assertEquals($expectedOutput, $processor->process($input));
    }
}
