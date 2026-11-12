<?php

declare(strict_types=1);

namespace Municipio\Content;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class CustomPostTypeTest extends TestCase
{
    #[DataProvider('displayNameProvider')]
    public function testGetDisplayName(array $typeDefinition, string $expected): void
    {
        $this->assertSame($expected, CustomPostType::getDisplayName($typeDefinition));
    }

    public static function displayNameProvider(): array
    {
        return [
            'uses display name when set'          => [['post_type_name' => 'Intranet News', 'display_name' => 'Nyheter'], 'Nyheter'],
            'falls back when display name empty'  => [['post_type_name' => 'Intranet News', 'display_name' => ''], 'Intranet News'],
            'falls back when only whitespace'     => [['post_type_name' => 'Intranet News', 'display_name' => '   '], 'Intranet News'],
            'falls back when display name absent' => [['post_type_name' => 'Intranet News'], 'Intranet News'],
            'trims display name'                  => [['post_type_name' => 'Intranet News', 'display_name' => ' Nyheter '], 'Nyheter'],
        ];
    }

    /**
     * The key is derived from the post type name only, so a display name
     * never changes which post type the posts belong to.
     */
    public function testPostTypeKeyIgnoresDisplayName(): void
    {
        $definition = ['post_type_name' => 'Intranet News', 'display_name' => 'Nyheter'];

        $this->assertSame(
            'intranet-news',
            CustomPostType::getPostTypeKey($definition['post_type_name'], self::sanitizeTitle(...))
        );
    }

    /**
     * Keys are truncated before sanitizing, as they always have been.
     * Changing the order would give a different key for multibyte names.
     */
    public function testPostTypeKeyTruncatesBeforeSanitizing(): void
    {
        $name = 'Förskola och utbildning';

        $this->assertSame('forskola-och-utbil', CustomPostType::getPostTypeKey($name, self::sanitizeTitle(...)));
        $this->assertNotSame(
            substr(self::sanitizeTitle($name), 0, 19),
            CustomPostType::getPostTypeKey($name, self::sanitizeTitle(...))
        );
    }

    /**
     * Simplified sanitize_title() for tests: transliterates Swedish characters,
     * lowercases and replaces everything else with dashes.
     */
    private static function sanitizeTitle(string $title): string
    {
        $title = strtr($title, ['å' => 'a', 'ä' => 'a', 'ö' => 'o', 'Å' => 'a', 'Ä' => 'a', 'Ö' => 'o']);
        $title = strtolower($title);
        $title = preg_replace('/[^a-z0-9]+/', '-', $title);

        return trim((string) $title, '-');
    }
}
