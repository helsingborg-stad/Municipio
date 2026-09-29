<?php

namespace Municipio\MarkupProcessor\Processors;

use PHPUnit\Framework\TestCase;

class MergeAdjacentLayerStylesProcessorTest extends TestCase
{
    public function testMergesImageRulesWithoutDroppingDistinctSelectors(): void
    {
        $first = '<style>@layer components {@container (min-width: 425px) {'
            . '.c-image.c-image--container-query .c-image--item-first {display: block;}}}</style>';
        $second = '<style>@layer components {@container (min-width: 425px) {'
            . '.c-image.c-image--container-query .c-image--item-second {display: block;}}}</style>';

        $output = (new MergeAdjacentLayerStylesProcessor())->process($first . "\n" . $second);

        $this->assertSame(1, substr_count($output, '<style>'));
        $this->assertStringContainsString('.c-image--item-first', $output);
        $this->assertStringContainsString('.c-image--item-second', $output);
        $this->assertSame(1, substr_count($output, '@container (min-width: 425px)'));
        $this->assertStringContainsString('.c-image--item-first, .c-image.c-image--container-query .c-image--item-second', $output);
    }

    public function testPreservesDifferentLayersAttributesAndInterveningMarkup(): void
    {
        $style = '<style>@layer components {.x {color: red}}</style>';
        $otherLayer = '<style>@layer wordpress {.x {color: blue}}</style>';
        $attributed = '<style id="custom">@layer components {.y {color: red}}</style>';
        $input = $style . $otherLayer . $attributed . '<div></div>' . $style;

        $this->assertSame($input, (new MergeAdjacentLayerStylesProcessor())->process($input));
    }

    public function testPreservesStyleWithRulesOutsideLayer(): void
    {
        $style = '<style>@layer components {.x {color: red}} .y {color: blue}</style>';
        $next = '<style>@layer components {.z {color: green}}</style>';

        $this->assertSame($style . $next, (new MergeAdjacentLayerStylesProcessor())->process($style . $next));
    }
}
