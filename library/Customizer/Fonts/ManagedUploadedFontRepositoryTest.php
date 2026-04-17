<?php

declare(strict_types=1);

namespace Municipio\Customizer\Fonts;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

/**
 * Tests managed uploaded font lookup.
 */
class ManagedUploadedFontRepositoryTest extends TestCase
{
    #[TestDox('getFonts() returns managed uploaded fonts from theme settings')]
    public function testGetFontsReturnsManagedUploadedFontsFromThemeSettings(): void
    {
        $wpService = new FakeWpService([
            'getThemeMod' => static fn(string $key, mixed $default): mixed => $key === FontCatalog::UPLOADED_FONTS_SETTING ? [['file' => 'https://example.com/open-sans.woff2'], ['file' => 21]] : $default,
        ]);

        $uploadedFontMapper = $this->createMock(UploadedFontMapper::class);
        $uploadedFontMapper
            ->expects(static::exactly(2))
            ->method('fromUploadValue')
            ->willReturnOnConsecutiveCalls(
                [
                    'id' => 0,
                    'name' => 'Open Sans',
                    'type' => 'woff2',
                    'url' => 'https://example.com/open-sans.woff2',
                ],
                [
                    'id' => 21,
                    'name' => 'Inter',
                    'type' => 'woff2',
                    'url' => 'https://example.com/inter.woff2',
                ],
            );

        $repository = new ManagedUploadedFontRepository($wpService, $uploadedFontMapper);

        static::assertSame(
            [
                'Open Sans' => [
                    'id' => 0,
                    'name' => 'Open Sans',
                    'type' => 'woff2',
                    'url' => 'https://example.com/open-sans.woff2',
                ],
                'Inter' => [
                    'id' => 21,
                    'name' => 'Inter',
                    'type' => 'woff2',
                    'url' => 'https://example.com/inter.woff2',
                ],
            ],
            $repository->getFonts(),
        );
    }

    #[TestDox('getFonts() ignores malformed theme settings rows')]
    public function testGetFontsIgnoresMalformedThemeSettingsRows(): void
    {
        $wpService = new FakeWpService([
            'getThemeMod' => static fn(string $key, mixed $default): mixed => $key === FontCatalog::UPLOADED_FONTS_SETTING ? [['file' => ''], ['name' => 'No File'], 'invalid'] : $default,
        ]);

        $uploadedFontMapper = $this->createMock(UploadedFontMapper::class);
        $uploadedFontMapper->expects(static::never())->method('fromUploadValue');

        $repository = new ManagedUploadedFontRepository($wpService, $uploadedFontMapper);

        static::assertSame([], $repository->getFonts());
    }
}
