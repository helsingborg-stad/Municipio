<?php

declare(strict_types=1);

namespace Modularity;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class DisplayTest extends TestCase
{
    /**
     * Verifies that post type slugs resolve to their module directories.
     */
    #[DataProvider('moduleDirectoryProvider')]
    public function testGetModuleDirectoryResolvesPostTypeSlug(string $postType, string $expectedDirectory): void
    {
        $display = (new \ReflectionClass(Display::class))->newInstanceWithoutConstructor();
        $findModuleDirectory = new \ReflectionMethod(Display::class, 'findModuleDirectory');
        $directories = [
            '/modules/NegativeMargin',
            '/modules/Spacer',
        ];

        static::assertSame($expectedDirectory, $findModuleDirectory->invoke($display, $postType, $directories));
    }

    /**
     * Provides post type slugs and their expected module directories.
     *
     * @return array<string, array{string, string}>
     */
    public static function moduleDirectoryProvider(): array
    {
        return [
            'hyphenated slug' => ['mod-negative-margin', 'NegativeMargin'],
            'single-word slug' => ['mod-spacer', 'Spacer'],
        ];
    }
}