<?php

declare(strict_types=1);

namespace Municipio\Customizer\Fonts;

use PHPUnit\Framework\TestCase;

/**
 * Tests font catalogue transformations.
 */
class ManagedFontsTest extends TestCase
{
    #[\PHPUnit\Framework\Attributes\TestDox('mergeUploadedFontRows() keeps one row per font family')]
    public function testMergeUploadedFontRowsKeepsOneRowPerFontFamily(): void
    {
        $managedFonts = new ManagedFonts();

        $rows = $managedFonts->mergeUploadedFontRows(
            [
                ['name' => 'Roboto Flex', 'file' => 10],
            ],
            [
                ['name' => 'Roboto Flex', 'file' => 25],
                ['name' => 'Inter', 'file' => 30],
            ],
        );

        static::assertSame(
            [
                ['name' => 'Roboto Flex', 'file' => 25],
                ['name' => 'Inter', 'file' => 30],
            ],
            $rows,
        );
    }
}
