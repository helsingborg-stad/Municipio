<?php

declare(strict_types=1);

namespace Municipio\Upgrade\V50;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class MigrateLegacyHeaderModifierCssTest extends TestCase
{
    #[TestDox('migrate rewrites business and casual header selectors in additional CSS')]
    public function testMigrateRewritesBusinessAndCasualHeaderSelectorsInAdditionalCss(): void
    {
        $themeStyles = '.c-header--business .foo{color:red}.c-header--casual .bar{color:blue}';

        $wpService = new FakeWpService([
            'wpGetCustomCss' => $themeStyles,
            'wpUpdateCustomCssPost' => static fn(string $css, array $args = []): \WP_Post|\WP_Error => new \WP_Error(),
            'getOption' => '',
            'updateOption' => true,
        ]);

        (new MigrateLegacyHeaderModifierCss($wpService))->migrate();

        static::assertSame(
            '.c-header--flexible .foo{color:red}.c-header--flexible .bar{color:blue}',
            $wpService->methodCalls['wpUpdateCustomCssPost'][0][0] ?? null,
        );
    }

    #[TestDox('migrate rewrites business secondary menu selectors to flexible lower area selectors')]
    public function testMigrateRewritesBusinessSecondaryMenuSelectorsToFlexibleLowerAreaSelectors(): void
    {
        $themeStyles = '.c-header.c-header--business .c-header__menu.c-header__menu--secondary .c-nav--depth-0>.c-nav__item>.c-nav__link{color:red}.c-header.c-header--business .c-header__menu.c-header__menu--secondary{background:#000}';

        $wpService = new FakeWpService([
            'wpGetCustomCss' => $themeStyles,
            'wpUpdateCustomCssPost' => static fn(string $css, array $args = []): \WP_Post|\WP_Error => new \WP_Error(),
            'getOption' => '',
            'updateOption' => true,
        ]);

        (new MigrateLegacyHeaderModifierCss($wpService))->migrate();

        static::assertSame(
            '#site-header-flexible-lower .c-nav--depth-0>.c-nav__item>.c-nav__link{color:red}#site-header-flexible-lower{background:#000}',
            $wpService->methodCalls['wpUpdateCustomCssPost'][0][0] ?? null,
        );
    }

    #[TestDox('migrate rewrites cached inline styles option when legacy modifiers are present')]
    public function testMigrateRewritesCachedInlineStylesOptionWhenLegacyModifiersArePresent(): void
    {
        $optionStyles = '.x--business{margin:0}.y--casual{padding:0}';

        $wpService = new FakeWpService([
            'wpGetCustomCss' => '',
            'wpUpdateCustomCssPost' => true,
            'getOption' => static fn(string $option, mixed $default = false): mixed => $option === 'municipio_customizer_inline_styles' ? $optionStyles : $default,
            'updateOption' => true,
        ]);

        (new MigrateLegacyHeaderModifierCss($wpService))->migrate();

        static::assertSame(
            ['municipio_customizer_inline_styles', '.x--flexible{margin:0}.y--flexible{padding:0}'],
            array_slice($wpService->methodCalls['updateOption'][0] ?? [], 0, 2),
        );
    }

    #[TestDox('migrate does not write when no legacy selectors are present')]
    public function testMigrateDoesNotWriteWhenNoLegacySelectorsArePresent(): void
    {
        $wpService = new FakeWpService([
            'wpGetCustomCss' => '.c-header--flexible .foo{color:red}',
            'wpUpdateCustomCssPost' => true,
            'getOption' => '.x--flexible{margin:0}',
            'updateOption' => true,
        ]);

        (new MigrateLegacyHeaderModifierCss($wpService))->migrate();

        static::assertCount(0, $wpService->methodCalls['wpUpdateCustomCssPost'] ?? []);
        static::assertCount(0, $wpService->methodCalls['updateOption'] ?? []);
    }
}
