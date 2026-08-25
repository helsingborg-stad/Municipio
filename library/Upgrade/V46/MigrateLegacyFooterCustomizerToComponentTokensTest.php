<?php

declare(strict_types=1);

namespace Municipio\Upgrade\V46;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

/**
 * Tests v46 migration of legacy footer customizer settings.
 */
class MigrateLegacyFooterCustomizerToComponentTokensTest extends TestCase
{
    #[TestDox('migrate maps legacy footer settings to footer component tokens and removes deprecated theme mods')]
    public function testMigrateMapsLegacyFooterSettingsToFooterComponentTokensAndRemovesDeprecatedThemeMods(): void
    {
        $legacyThemeMods = [
            'tokens' => '{"token":{"--color--primary":"#f00"},"component":{"__general__":{"button":{"--c-button--border-radius":"2px"}}}}',
            'footer_style' => 'basic',
            'footer_columns' => 4,
            'footer_padding' => 6,
            'footer_logotype_alignment' => 'align-center',
            'footer_text_alignment' => 'u-text-align--right',
            'pre_footer_text_alignment' => 'u-text-align--center',
            'footer_header_border' => true,
            'footer_color_text' => '#111111',
            'footer_background' => [
                'background-color' => '#222222',
                'background-image' => 'https://example.com/footer.svg',
                'background-repeat' => 'repeat-x',
                'background-position' => 'left top',
                'background-size' => 'contain',
            ],
            'footer_subfooter_colors' => [
                'background' => '#333333',
                'text' => '#eeeeee',
                'separator' => '#999999',
            ],
            'footer_subfooter_height_logotype' => 7,
            'footer_subfooter_alignment' => 'space-between',
        ];

        $wpService = new FakeWpService([
            'getThemeMod' => static fn(string $name, mixed $default = null): mixed => $legacyThemeMods[$name] ?? $default,
            'setThemeMod' => true,
            'removeThemeMod' => true,
        ]);

        (new MigrateLegacyFooterCustomizerToComponentTokens($wpService))->migrate();

        $setCalls = $wpService->methodCalls['setThemeMod'] ?? [];
        static::assertCount(2, $setCalls);
        static::assertSame('footer_background_image', $setCalls[0][0]);
        static::assertSame('https://example.com/footer.svg', $setCalls[0][1]);
        static::assertSame('tokens', $setCalls[1][0]);

        $storedTokens = json_decode((string) $setCalls[1][1], true);

        static::assertSame('#f00', $storedTokens['token']['--color--primary']);
        static::assertSame('2px', $storedTokens['component']['__general__']['button']['--c-button--border-radius']);

        $footerTokens = $storedTokens['component']['__general__']['footer'];

        static::assertSame(4, $footerTokens['--c-footer--columns-count']);
        static::assertEquals(2.0, $footerTokens['--c-footer--outer-padding-inset-multiplier-scale']);
        static::assertSame('center', $footerTokens['--c-footer--logotype-justify-content']);
        static::assertSame('right', $footerTokens['--c-footer--text-align']);
        static::assertSame('center', $footerTokens['--c-footer--prefooter-text-align']);
        static::assertSame('var(--c-footer--subfooter-border-width-token)', $footerTokens['--c-footer--subfooter-border-width']);
        static::assertSame('#111111', $footerTokens['--c-footer--color']);
        static::assertSame('#222222', $footerTokens['--c-footer--background-color']);
        static::assertSame('repeat-x', $footerTokens['--c-footer--background-repeat']);
        static::assertSame('left top', $footerTokens['--c-footer--background-position']);
        static::assertSame('contain', $footerTokens['--c-footer--background-size']);
        static::assertSame('#333333', $footerTokens['--c-footer--subfooter-background-color']);
        static::assertSame('#eeeeee', $footerTokens['--c-footer--subfooter-color']);
        static::assertSame('#999999', $footerTokens['--c-footer--subfooter-separator-color']);
        static::assertEquals(7.0, $footerTokens['--c-footer--subfooter-logotype-height-multiplier']);
        static::assertSame('space-between', $footerTokens['--c-footer--subfooter-alignment']);

        static::assertSame(
            [
                ['footer_style'],
                ['footer_columns'],
                ['footer_padding'],
                ['footer_logotype_alignment'],
                ['footer_text_alignment'],
                ['pre_footer_text_alignment'],
                ['footer_header_border'],
                ['footer_header_border_size'],
                ['footer_header_border_color'],
                ['footer_color_text'],
                ['footer_background'],
                ['footer_subfooter_colors'],
                ['footer_subfooter_height_logotype'],
                ['footer_subfooter_padding'],
                ['footer_subfooter_alignment'],
            ],
            $wpService->methodCalls['removeThemeMod'] ?? [],
        );
    }

    #[TestDox('migrate keeps an existing dedicated footer background image setting')]
    public function testMigrateKeepsAnExistingDedicatedFooterBackgroundImageSetting(): void
    {
        $legacyThemeMods = [
            'tokens' => '',
            'footer_background_image' => 'https://example.com/current-footer.svg',
            'footer_background' => [
                'background-image' => 'https://example.com/legacy-footer.svg',
            ],
        ];

        $wpService = new FakeWpService([
            'getThemeMod' => static fn(string $name, mixed $default = null): mixed => $legacyThemeMods[$name] ?? $default,
            'setThemeMod' => true,
            'removeThemeMod' => true,
        ]);

        (new MigrateLegacyFooterCustomizerToComponentTokens($wpService))->migrate();

        $setCalls = $wpService->methodCalls['setThemeMod'] ?? [];

        static::assertCount(1, $setCalls);
        static::assertSame('tokens', $setCalls[0][0]);

        $storedTokens = json_decode((string) $setCalls[0][1], true);

        static::assertSame(1, $storedTokens['component']['__general__']['footer']['--c-footer--columns-count']);
    }

    #[TestDox('migrate maps legacy column count when the footer used the column layout')]
    public function testMigrateMapsLegacyColumnCountWhenFooterUsedTheColumnLayout(): void
    {
        $legacyThemeMods = [
            'tokens' => '',
            'footer_style' => 'columns',
            'footer_columns' => 4,
        ];

        $wpService = new FakeWpService([
            'getThemeMod' => static fn(string $name, mixed $default = null): mixed => $legacyThemeMods[$name] ?? $default,
            'setThemeMod' => true,
            'removeThemeMod' => true,
        ]);

        (new MigrateLegacyFooterCustomizerToComponentTokens($wpService))->migrate();

        $setCalls = $wpService->methodCalls['setThemeMod'] ?? [];
        static::assertCount(1, $setCalls);
        static::assertSame('tokens', $setCalls[0][0]);

        $storedTokens = json_decode((string) $setCalls[0][1], true);

        static::assertSame(4, $storedTokens['component']['__general__']['footer']['--c-footer--columns-count']);
    }

    #[TestDox('migrate seeds a one-column footer token when legacy footer settings are absent and still removes deprecated theme mods')]
    public function testMigrateSeedsAOneColumnFooterTokenWhenLegacyFooterSettingsAreAbsentAndStillRemovesDeprecatedThemeMods(): void
    {
        $wpService = new FakeWpService([
            'getThemeMod' => static fn(string $name, mixed $default = null): mixed => $name === 'tokens' ? '' : $default,
            'setThemeMod' => true,
            'removeThemeMod' => true,
        ]);

        (new MigrateLegacyFooterCustomizerToComponentTokens($wpService))->migrate();

        static::assertCount(1, $wpService->methodCalls['setThemeMod'] ?? []);
        static::assertSame('tokens', $wpService->methodCalls['setThemeMod'][0][0]);

        $storedTokens = json_decode((string) $wpService->methodCalls['setThemeMod'][0][1], true);

        static::assertSame(1, $storedTokens['component']['__general__']['footer']['--c-footer--columns-count']);
        static::assertSame('center', $storedTokens['component']['__general__']['footer']['--c-footer--subfooter-alignment']);
        static::assertCount(15, $wpService->methodCalls['removeThemeMod'] ?? []);
    }
}
