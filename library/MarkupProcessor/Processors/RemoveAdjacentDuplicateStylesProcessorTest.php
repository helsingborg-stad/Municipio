<?php

namespace Municipio\MarkupProcessor\Processors;

use PHPUnit\Framework\TestCase;

class RemoveAdjacentDuplicateStylesProcessorTest extends TestCase
{
    public function testRemovesOnlyConsecutiveIdenticalStyles(): void
    {
        $style = '<style>@layer wordpress {.icon {opacity: 1}}</style>';
        $other = '<style>@layer wordpress {.icon {opacity: 0}}</style>';
        $input = $style . "\n  " . $style . $other . $style . '<div></div>' . $style;

        $expected = $style . $other . $style . '<div></div>' . $style;

        $this->assertSame($expected, (new RemoveAdjacentDuplicateStylesProcessor())->process($input));
    }

    public function testPreservesDifferentStyleAttributes(): void
    {
        $input = '<style media="screen">.x {color: red}</style>'
            . '<style media="print">.x {color: red}</style>';

        $this->assertSame($input, (new RemoveAdjacentDuplicateStylesProcessor())->process($input));
    }

    public function testDoesNotLeaveBlankLinesForRemovedStyles(): void
    {
        $style = '<style>.icon {opacity: 1}</style>';
        $next = '<style>.image {display: block}</style>';
        $input = "  $style\n  $style\n  $style\n  $next";

        $this->assertSame("  $style\n  $next", (new RemoveAdjacentDuplicateStylesProcessor())->process($input));
    }
}
