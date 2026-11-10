<?php

declare(strict_types=1);

namespace Municipio\Content;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class CustomPostTypeTest extends TestCase
{
    private const NAME = 'field_56b3619c5defc';
    private const DISPLAY = 'field_6912b0c4a7e51';

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

    public function testNormalizeStoresKeyAndKeepsNameAsDisplayName(): void
    {
        $rows = CustomPostType::normalizeTypeDefinitionRows(
            ['row-0' => [self::NAME => 'Intranet News', self::DISPLAY => '']],
            self::sanitizeTitle(...)
        );

        $this->assertSame('intranet-news', $rows['row-0'][self::NAME]);
        $this->assertSame('Intranet News', $rows['row-0'][self::DISPLAY]);
    }

    public function testNormalizeKeepsExistingDisplayName(): void
    {
        $rows = CustomPostType::normalizeTypeDefinitionRows(
            ['row-0' => [self::NAME => 'Intranet News', self::DISPLAY => 'Nyheter']],
            self::sanitizeTitle(...)
        );

        $this->assertSame('intranet-news', $rows['row-0'][self::NAME]);
        $this->assertSame('Nyheter', $rows['row-0'][self::DISPLAY]);
    }

    public function testNormalizeLeavesExistingKeyUntouched(): void
    {
        $input = ['row-0' => [self::NAME => 'intranet-news', self::DISPLAY => '']];

        $this->assertSame($input, CustomPostType::normalizeTypeDefinitionRows($input, self::sanitizeTitle(...)));
    }

    /**
     * Truncating before sanitizing must give the same key as registration,
     * even when multibyte characters make the two orders differ.
     */
    public function testNormalizeKeepsTheRegisteredKeyForTruncatedNames(): void
    {
        $name = 'Förskola och utbildning';
        $rows = CustomPostType::normalizeTypeDefinitionRows(
            [[self::NAME => $name, self::DISPLAY => '']],
            self::sanitizeTitle(...)
        );

        $this->assertSame('forskola-och-utbil', $rows[0][self::NAME]);
        $this->assertSame(CustomPostType::getPostTypeKey($name, self::sanitizeTitle(...)), $rows[0][self::NAME]);
        $this->assertNotSame(substr(self::sanitizeTitle($name), 0, 19), $rows[0][self::NAME]);
    }

    public function testNormalizeIsIdempotent(): void
    {
        $input = [
            [self::NAME => 'Förskola och utbildning', self::DISPLAY => ''],
            [self::NAME => 'Bo, bygga och miljö', self::DISPLAY => 'Bo och bygga'],
            [self::NAME => ' Leading space', self::DISPLAY => ''],
        ];

        $once  = CustomPostType::normalizeTypeDefinitionRows($input, self::sanitizeTitle(...));
        $twice = CustomPostType::normalizeTypeDefinitionRows($once, self::sanitizeTitle(...));

        $this->assertSame($once, $twice);

        foreach ($input as $index => $row) {
            $this->assertSame(
                CustomPostType::getPostTypeKey($row[self::NAME], self::sanitizeTitle(...)),
                $once[$index][self::NAME]
            );
        }
    }

    public function testNormalizeSupportsRowsKeyedByFieldName(): void
    {
        $rows = CustomPostType::normalizeTypeDefinitionRows(
            [['post_type_name' => 'Intranet News', 'slug' => 'nyheter']],
            self::sanitizeTitle(...)
        );

        $this->assertSame('intranet-news', $rows[0]['post_type_name']);
        $this->assertSame('Intranet News', $rows[0]['display_name']);
        $this->assertSame('nyheter', $rows[0]['slug']);
    }

    public function testNormalizeSkipsEmptyAndNonArrayRows(): void
    {
        $input = [
            'acfcloneindex' => 'not a row',
            'row-0'         => [self::NAME => '', self::DISPLAY => ''],
            'row-1'         => ['other_field' => 'value'],
        ];

        $this->assertSame($input, CustomPostType::normalizeTypeDefinitionRows($input, self::sanitizeTitle(...)));
    }

    public function testFillMissingDisplayNamesUsesPostTypeName(): void
    {
        $rows = CustomPostType::fillMissingDisplayNames([
            [self::NAME => 'Intranet News', self::DISPLAY => ''],
            [self::NAME => ' Driftstörningar ', self::DISPLAY => '  '],
            [self::NAME => 'System'],
        ]);

        $this->assertSame('Intranet News', $rows[0][self::DISPLAY]);
        $this->assertSame('Driftstörningar', $rows[1][self::DISPLAY]);
        $this->assertSame('System', $rows[2][self::DISPLAY]);
        $this->assertSame('Intranet News', $rows[0][self::NAME], 'Post type name must not change');
    }

    public function testFillMissingDisplayNamesKeepsExistingDisplayName(): void
    {
        $input = [[self::NAME => 'intranet-news', self::DISPLAY => 'Nyheter']];

        $this->assertSame($input, CustomPostType::fillMissingDisplayNames($input));
    }

    public function testFillMissingDisplayNamesSupportsRowsKeyedByFieldName(): void
    {
        $rows = CustomPostType::fillMissingDisplayNames([['post_type_name' => 'Intranet News']]);

        $this->assertSame('Intranet News', $rows[0]['display_name']);
    }

    public function testFillMissingDisplayNamesSkipsEmptyAndNonArrayRows(): void
    {
        $input = ['acfcloneindex' => 'x', 'row-0' => [self::NAME => '', self::DISPLAY => ''], 'row-1' => ['other' => 1]];

        $this->assertSame($input, CustomPostType::fillMissingDisplayNames($input));
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
