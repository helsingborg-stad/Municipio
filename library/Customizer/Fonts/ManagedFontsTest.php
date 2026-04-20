<?php

declare(strict_types=1);

namespace Municipio\Customizer\Fonts;

use PHPUnit\Framework\TestCase;

/**
 * Tests font catalogue transformations.
 */
class ManagedFontsTest extends TestCase
{
    #[\PHPUnit\Framework\Attributes\TestDox('mergeUploadedFontRows() keeps one row per uploaded file')]
    public function testMergeUploadedFontRowsKeepsOneRowPerUploadedFile(): void
    {
        $managedFonts = new ManagedFonts();

        $rows = $managedFonts->mergeUploadedFontRows(
            [
                ['file' => 10],
            ],
            [
                ['file' => 10],
                ['file' => 30],
            ],
        );

        static::assertSame(
            [
                ['file' => 10],
                ['file' => 30],
            ],
            $rows,
        );
    }
}
