<?php

declare(strict_types=1);

namespace Municipio\Blocks;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class ColumnsTest extends TestCase
{
    #[TestDox('removes flex-basis from column inline styles while preserving other declarations')]
    public function testRenderBlockColumnsRemovesOnlyFlexBasisFromStyleAttribute(): void
    {
        $sut = new Columns();

        $content = '<div class="wp-block-columns"><div class="wp-block-column is-layout-flow" style="flex-basis:66.66%; color:red;">Content</div></div>';
        $block = [
            'blockName'   => 'core/columns',
            'innerBlocks' => [
                [
                    'attrs' => [
                        'width' => '66.66%',
                    ],
                ],
            ],
        ];

        $result = $sut->renderBlockColumns($content, $block);

        $this->assertStringNotContainsString('flex-basis', $result);
        $this->assertStringContainsString('style="color:red;"', $result);
        $this->assertStringContainsString('o-grid-column-block', $result);
    }

    #[TestDox('removes the style attribute when flex-basis was the only inline declaration')]
    public function testRenderBlockColumnsRemovesEmptyStyleAttribute(): void
    {
        $sut = new Columns();

        $content = '<div class="wp-block-columns"><div class="wp-block-column is-layout-flow" style="flex-basis:66.66%;">Content</div></div>';
        $block = [
            'blockName'   => 'core/columns',
            'innerBlocks' => [
                [
                    'attrs' => [
                        'width' => '66.66%',
                    ],
                ],
            ],
        ];

        $result = $sut->renderBlockColumns($content, $block);

        $this->assertStringNotContainsString('flex-basis', $result);
        $this->assertStringNotContainsString('style=""', $result);
        $this->assertStringNotContainsString(' style=', $result);
    }
}
