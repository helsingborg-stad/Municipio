<?php

namespace Municipio\StyleguideCss\ApplyLayerToInlineStyles;

use Municipio\HooksRegistrar\Hookable;
use Municipio\MarkupProcessor\MarkupProcessorInterface;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Contracts\AddFilter;

class ApplyLayerToInlineStylesTest extends TestCase
{
    #[TestDox('wraps inline styles in a @layer wordpress {}')]
    public function testProcess(): void
    {
        $wpServiceMock = $this->createMock(AddFilter::class);
        $applyLayer = new ApplyLayerToInlineStyles($wpServiceMock);

        $input = '<style>body { background: red; }</style>';
        $expectedOutput = '<style>@layer wordpress {body { background: red; }}</style>';

        $this->assertEquals($expectedOutput, $applyLayer->process($input));
    }

    #[TestDox('does not wrap styles that already contain a @layer')]
    public function testProcessWithExistingLayer(): void
    {
        $wpServiceMock = $this->createMock(AddFilter::class);
        $applyLayer = new ApplyLayerToInlineStyles($wpServiceMock);

        $input = '<style>@layer wordpress {body { background: red; }}</style>';
        $expectedOutput = '<style>@layer wordpress {body { background: red; }}</style>';

        $this->assertEquals($expectedOutput, $applyLayer->process($input));
    }

    #[TestDox('does not wrap styles that contain a layer(')]
    public function testProcessWithLayerFunction(): void
    {
        $wpServiceMock = $this->createMock(AddFilter::class);
        $applyLayer = new ApplyLayerToInlineStyles($wpServiceMock);

        $input = '<style>@import url("style.css") layer(theme);</style>';
        $expectedOutput = '<style>@import url("style.css") layer(theme);</style>';

        $this->assertEquals($expectedOutput, $applyLayer->process($input));
    }
}
