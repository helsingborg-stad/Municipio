<?php

namespace Municipio\PostsList\ViewCallableProviders\Table\TableArguments;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class TableItemsWithEmpasizedFirstItemTest extends TestCase
{
    #[TestDox('wraps all columns except the first one in <span> tags')]
    public function testEmphasize(): void
    {
        $items = [
            [
                'columns' => [
                    'First column',
                    'Second column',
                    'Third column',
                ],
            ],
            [
                'columns' => [
                    'First column of second item',
                    'Second column of second item',
                ],
            ],
        ];

        $emphasizer = new TableItemsWithEmpasizedFirstItem($items);
        $result = $emphasizer->emphasize();

        $this->assertSame('First column', $result[0]['columns'][0]);
        $this->assertSame('<span class="c-typography c-typography__variant--meta">Second column</span>', $result[0]['columns'][1]);
        $this->assertSame('<span class="c-typography c-typography__variant--meta">Third column</span>', $result[0]['columns'][2]);
        $this->assertSame('First column of second item', $result[1]['columns'][0]);
        $this->assertSame('<span class="c-typography c-typography__variant--meta">Second column of second item</span>', $result[1]['columns'][1]);
    }
}
