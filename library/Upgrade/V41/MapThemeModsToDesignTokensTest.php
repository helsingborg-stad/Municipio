<?php

namespace Municipio\Upgrade\V41;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class MapThemeModsToDesignTokensTest extends TestCase
{
    #[TestDox('maps single general token')]
    public function testMapsSingleGeneralToken(): void
    {
        $themeMods = ['color_background' => '#000001'];
        $themeModsTokenMap = ['color_background' => 'token.--color--background'];

        $migratedTokens = (new MapThemeModsToDesignTokens())->map($themeMods, $themeModsTokenMap);

        static::assertSame('#000001', $migratedTokens['token']['--color--background']);
    }

    #[TestDox('maps nested value')]
    public function testMapsNestedValue(): void
    {
        $themeMods = ['color_background' => ['background' => '#000001']];
        $themeModsTokenMap = ['color_background.background' => 'token.--color--background'];

        $migratedTokens = (new MapThemeModsToDesignTokens())->map($themeMods, $themeModsTokenMap);

        static::assertSame('#000001', $migratedTokens['token']['--color--background']);
    }

    #[TestDox('maps to multiple tokens')]
    public function testMapsToMultipleTokens(): void
    {
        $themeMods = ['color_background' => '#000001'];
        $themeModsTokenMap = [
            'color_background' => ['token.--color--background', 'token.--color--foreground'],
        ];

        $migratedTokens = (new MapThemeModsToDesignTokens())->map($themeMods, $themeModsTokenMap);

        static::assertSame('#000001', $migratedTokens['token']['--color--background']);
        static::assertSame('#000001', $migratedTokens['token']['--color--foreground']);
    }

    #[TestDox('maps to component token')]
    public function testMapsToComponentToken(): void
    {
        $themeMods = ['footer_subfooter_colors' => ['background' => '#000001']];
        $themeModsTokenMap = ['footer_subfooter_colors.background' => 'component.__general__.footer.--c-footer--subfooter-color-background'];

        $migratedTokens = (new MapThemeModsToDesignTokens())->map($themeMods, $themeModsTokenMap);

        static::assertSame('#000001', $migratedTokens['component']['__general__']['footer']['--c-footer--subfooter-color-background']);
    }

    #[TestDox('maps to scoped component token')]
    public function testMapsToScopedComponentToken(): void
    {
        $themeMods = ['quicklinks_custom_colors' => ['background' => '#000001']];
        $themeModsTokenMap = ['quicklinks_custom_colors.background' => 'component.scope:s-quicklinks-header.header.--c-header--background-color'];

        $migratedTokens = (new MapThemeModsToDesignTokens())->map($themeMods, $themeModsTokenMap);

        static::assertSame('#000001', $migratedTokens['component']['scope:s-quicklinks-header']['header']['--c-header--background-color']);
    }

    #[TestDox('ignores non-existing theme mod')]
    public function testIgnoresNonExistingThemeMod(): void
    {
        $themeMods = [];
        $themeModsTokenMap = ['non_existing' => 'token.--color--background'];

        $migratedTokens = (new MapThemeModsToDesignTokens())->map($themeMods, $themeModsTokenMap);

        static::assertArrayNotHasKey('--color--background', $migratedTokens['token']);
    }

    #[TestDox('maps legacy header logotype height to the logotype height multiplier token')]
    public function testMapsHeaderLogotypeHeightToLogotypeHeightMultiplierToken(): void
    {
        $themeMods = ['header_logotype_height' => 6];
        $themeModsTokenMap = [
            'header_logotype_height' => 'component.__general__.header.--c-header--logotype-height-multiplier',
        ];

        $migratedTokens = (new MapThemeModsToDesignTokens())->map($themeMods, $themeModsTokenMap);

        static::assertSame(6, $migratedTokens['component']['__general__']['header']['--c-header--logotype-height-multiplier']);
    }

    #[TestDox('maps legacy footer logotype height to the home link height multiplier token')]
    public function testMapsFooterLogotypeHeightToHomeLinkHeightMultiplierToken(): void
    {
        $themeMods = ['footer_height_logotype' => 6];
        $themeModsTokenMap = [
            'footer_height_logotype' => 'component.__general__.footer.--c-footer--home-link-height-multiplier',
        ];

        $migratedTokens = (new MapThemeModsToDesignTokens())->map($themeMods, $themeModsTokenMap);

        static::assertSame(6, $migratedTokens['component']['__general__']['footer']['--c-footer--home-link-height-multiplier']);
    }
}
