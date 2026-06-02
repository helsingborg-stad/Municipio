<?php

declare(strict_types=1);

namespace Municipio\Customizer\Fonts;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class NativeFontWriterOutcomeTestState
{
    /**
     * @var array<int, array<string, mixed>>
     */
    public static array $insertedPosts = [];

    /**
     * @var array<int, array<int, mixed>>
     */
    public static array $addedPostMeta = [];

    public static function reset(): void
    {
        self::$insertedPosts = [];
        self::$addedPostMeta = [];
    }
}

function wp_insert_post(array $postarr, bool $wpError = false): int
{
    NativeFontWriterOutcomeTestState::$insertedPosts[] = array_map(
        static function (mixed $value): mixed {
            if (!is_string($value)) {
                return $value;
            }

            return stripslashes($value);
        },
        $postarr,
    );

    return count(NativeFontWriterOutcomeTestState::$insertedPosts);
}

function wp_json_encode(mixed $value): string|false
{
    return json_encode($value);
}

function is_wp_error(mixed $thing): bool
{
    return false;
}

function add_post_meta(int $postId, string $metaKey, mixed $metaValue): bool
{
    NativeFontWriterOutcomeTestState::$addedPostMeta[] = [$postId, $metaKey, $metaValue];

    return true;
}

class NativeFontWriterOutcomeTest extends TestCase
{
    protected function setUp(): void
    {
        NativeFontWriterOutcomeTestState::reset();
    }

    #[TestDox('createFontFamilyIfMissing() stores native family settings with name slug font family and preview')]
    public function testCreateFontFamilyIfMissingStoresNativeFamilySettingsWithNameSlugFontFamilyAndPreview(): void
    {
        $support = $this->createMock(NativeFontLibrarySupport::class);
        $support->method('isAvailable')->willReturn(true);
        $support->method('sanitizeSlug')->with('Roboto')->willReturn('roboto');
        $support->method('getFontFamilyBySlug')->with('roboto')->willReturn(null);

        $writer = new NativeFontFamilyWriter($support);

        $postId = $writer->createFontFamilyIfMissing('Roboto', '"Roboto", sans-serif', 'https://example.com/roboto.svg');

        static::assertSame(1, $postId);
        static::assertCount(1, NativeFontWriterOutcomeTestState::$insertedPosts);

        $insertedPost = NativeFontWriterOutcomeTestState::$insertedPosts[0];
        $settings = json_decode((string) $insertedPost['post_content'], true);

        static::assertSame('wp_font_family', $insertedPost['post_type']);
        static::assertSame('Roboto', $insertedPost['post_title']);
        static::assertSame('roboto', $insertedPost['post_name']);
        static::assertSame(
            [
                'name' => 'Roboto',
                'slug' => 'roboto',
                'fontFamily' => '"Roboto", sans-serif',
                'preview' => 'https://example.com/roboto.svg',
            ],
            $settings,
        );
    }

    #[TestDox('createFontFaceIfMissing() stores native face settings with defined style weight unicode range and preview')]
    public function testCreateFontFaceIfMissingStoresNativeFaceSettingsWithDefinedStyleWeightUnicodeRangeAndPreview(): void
    {
        $support = $this->createMock(NativeFontLibrarySupport::class);
        $support->method('isAvailable')->willReturn(true);
        $support->method('fontFaceExists')->with(21, ['https://fonts.example.com/roboto-400.woff2'])->willReturn(false);
        $support->method('getFontFamilyCssValue')->with('Roboto')->willReturn('"Roboto", sans-serif');
        $support->method('sanitizeSlug')->willReturnCallback(static fn(string $value): string => $value);

        $writer = new NativeFontFaceWriter($support);

        $writer->createFontFaceIfMissing(
            21,
            'Roboto',
            ['https://fonts.example.com/roboto-400.woff2'],
            'normal',
            '400',
            unicodeRange: 'U+0000-00FF',
            preview: 'https://example.com/roboto-400-normal.svg',
        );

        static::assertCount(1, NativeFontWriterOutcomeTestState::$insertedPosts);

        $insertedPost = NativeFontWriterOutcomeTestState::$insertedPosts[0];
        $settings = json_decode((string) $insertedPost['post_content'], true);

        static::assertSame('wp_font_face', $insertedPost['post_type']);
        static::assertSame(21, $insertedPost['post_parent']);
        static::assertIsString($insertedPost['post_title']);
        static::assertNotSame('', $insertedPost['post_title']);
        static::assertSame($insertedPost['post_title'], $insertedPost['post_name']);
        static::assertSame('"Roboto", sans-serif', $settings['fontFamily']);
        static::assertSame('normal', $settings['fontStyle']);
        static::assertSame('400', $settings['fontWeight']);
        static::assertSame('swap', $settings['fontDisplay']);
        static::assertSame(['https://fonts.example.com/roboto-400.woff2'], $settings['src']);
        static::assertSame('U+0000-00FF', $settings['unicodeRange']);
        static::assertSame('https://example.com/roboto-400-normal.svg', $settings['preview']);
    }
}
