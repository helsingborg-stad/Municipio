<?php

declare(strict_types=1);

namespace Municipio\Customizer\Fonts;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

/**
 * Tests uploaded font aggregation.
 */
class FontRepositoryTest extends TestCase
{
    #[TestDox('getUploadedFonts() returns managed uploaded fonts')]
    public function testGetUploadedFontsReturnsManagedUploadedFonts(): void
    {
        $managedUploadedFontRepository = $this->createMock(ManagedUploadedFontRepository::class);
        $managedUploadedFontRepository
            ->expects(static::once())
            ->method('getFonts')
            ->willReturn([
                'Inter|https://example.com/inter-managed.woff2' => [
                    'id' => 21,
                    'name' => 'Inter',
                    'type' => 'woff2',
                    'url' => 'https://example.com/inter-managed.woff2',
                ],
            ]);

        $repository = new FontRepository($managedUploadedFontRepository);

        static::assertSame(
            [
                'Inter|https://example.com/inter-managed.woff2' => [
                    'id' => 21,
                    'name' => 'Inter',
                    'type' => 'woff2',
                    'url' => 'https://example.com/inter-managed.woff2',
                ],
            ],
            $repository->getUploadedFonts(),
        );
    }

    #[TestDox('addFontMimes() adds WOFF mime types')]
    public function testAddFontMimesAddsWoffMimeTypes(): void
    {
        $managedUploadedFontRepository = $this->createMock(ManagedUploadedFontRepository::class);

        $repository = new FontRepository($managedUploadedFontRepository);

        static::assertSame(
            [
                'jpg' => 'image/jpeg',
                'woff' => 'application/font-woff',
                'woff2' => 'application/font-woff2',
            ],
            $repository->addFontMimes(['jpg' => 'image/jpeg']),
        );
    }
}
