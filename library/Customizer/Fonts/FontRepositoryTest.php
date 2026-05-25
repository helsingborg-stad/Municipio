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
    #[TestDox('getUploadedFonts() merges legacy and managed fonts with managed precedence')]
    public function testGetUploadedFontsMergesLegacyAndManagedFontsWithManagedPrecedence(): void
    {
        $managedUploadedFontRepository = $this->createMock(ManagedUploadedFontRepository::class);
        $managedUploadedFontRepository
            ->expects(static::once())
            ->method('getFonts')
            ->willReturn([
                'Inter' => [
                    'id' => 21,
                    'name' => 'Inter',
                    'type' => 'woff2',
                    'url' => 'https://example.com/inter-managed.woff2',
                ],
            ]);

        $legacyUploadedFontRepository = $this->createMock(LegacyUploadedFontRepository::class);
        $legacyUploadedFontRepository
            ->expects(static::once())
            ->method('getFonts')
            ->willReturn([
                'Roboto' => [
                    'id' => 10,
                    'name' => 'Roboto',
                    'type' => 'woff2',
                    'url' => 'https://example.com/roboto.woff2',
                ],
                'Inter' => [
                    'id' => 11,
                    'name' => 'Inter',
                    'type' => 'woff2',
                    'url' => 'https://example.com/inter-legacy.woff2',
                ],
            ]);

        $repository = new FontRepository($managedUploadedFontRepository, $legacyUploadedFontRepository);

        static::assertSame(
            [
                'Roboto' => [
                    'id' => 10,
                    'name' => 'Roboto',
                    'type' => 'woff2',
                    'url' => 'https://example.com/roboto.woff2',
                ],
                'Inter' => [
                    'id' => 21,
                    'name' => 'Inter',
                    'type' => 'woff2',
                    'url' => 'https://example.com/inter-managed.woff2',
                ],
            ],
            $repository->getUploadedFonts(),
        );
    }

    #[TestDox('addFontMimes() delegates to the legacy repository')]
    public function testAddFontMimesDelegatesToTheLegacyRepository(): void
    {
        $managedUploadedFontRepository = $this->createMock(ManagedUploadedFontRepository::class);

        $legacyUploadedFontRepository = $this->createMock(LegacyUploadedFontRepository::class);
        $legacyUploadedFontRepository
            ->expects(static::once())
            ->method('addFontMimes')
            ->with(['jpg' => 'image/jpeg'])
            ->willReturn([
                'jpg' => 'image/jpeg',
                'woff' => 'application/font-woff',
            ]);

        $repository = new FontRepository($managedUploadedFontRepository, $legacyUploadedFontRepository);

        static::assertSame(
            [
                'jpg' => 'image/jpeg',
                'woff' => 'application/font-woff',
            ],
            $repository->addFontMimes(['jpg' => 'image/jpeg']),
        );
    }
}
