<?php

declare(strict_types=1);

namespace Municipio\Upgrade\V55;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class MigrateFlexibleHeaderContainerSelectorsTest extends TestCase
{
    #[TestDox('migrate rewrites lower and upper container selectors in additional CSS')]
    public function testMigrateRewritesLowerAndUpperContainerSelectorsInAdditionalCss(): void
    {
        $css = '#site-header-flexible-lower .c-header__main-lower-area-container{background:#000}#site-header-flexible-upper .c-header__main-upper-area-container{background:#fff}';

        $wpService = new FakeWpService([
            'wpGetCustomCss' => $css,
            'wpUpdateCustomCssPost' => static fn(string $css, array $args = []): \WP_Post|\WP_Error => new \WP_Error(),
            'getOption' => '',
            'updateOption' => true,
        ]);

        $this->createMigration($wpService)->migrate();

        static::assertSame(
            '#site-header-flexible-lower{background:#000}#site-header-flexible-upper{background:#fff}',
            $wpService->methodCalls['wpUpdateCustomCssPost'][0][0] ?? null,
        );
    }

    #[TestDox('migrate rewrites encoded lower nav selector in additional CSS')]
    public function testMigrateRewritesEncodedLowerNavSelectorInAdditionalCss(): void
    {
        $css = '#site-header-flexible-lower .c-header__main-lower-area-container .c-nav--depth-0&gt;.c-nav__item&gt;.c-nav__link{color:red}';

        $wpService = new FakeWpService([
            'wpGetCustomCss' => $css,
            'wpUpdateCustomCssPost' => static fn(string $css, array $args = []): \WP_Post|\WP_Error => new \WP_Error(),
            'getOption' => '',
            'updateOption' => true,
        ]);

        $this->createMigration($wpService)->migrate();

        static::assertSame(
            '#site-header-flexible-lower .c-nav--depth-0>.c-nav__item>.c-nav__link{color:red}',
            $wpService->methodCalls['wpUpdateCustomCssPost'][0][0] ?? null,
        );
    }

    #[TestDox('migrate rewrites stale selectors in inline styles option')]
    public function testMigrateRewritesStaleSelectorsInInlineStylesOption(): void
    {
        $optionCss = '#site-header-flexible-lower .c-header__main-lower-area-container{background:#000}';

        $wpService = new FakeWpService([
            'wpGetCustomCss' => '',
            'wpUpdateCustomCssPost' => true,
            'getOption' => static fn(string $option, mixed $default = false): mixed => $option === 'municipio_customizer_inline_styles' ? $optionCss : $default,
            'updateOption' => true,
        ]);

        $this->createMigration($wpService)->migrate();

        static::assertSame(
            ['municipio_customizer_inline_styles', '#site-header-flexible-lower{background:#000}'],
            array_slice($wpService->methodCalls['updateOption'][0] ?? [], 0, 2),
        );
    }

    #[TestDox('migrate does nothing when no stale selectors are present')]
    public function testMigrateDoesNothingWhenNoStaleSelectorsArePresent(): void
    {
        $wpService = new FakeWpService([
            'wpGetCustomCss' => '#site-header-flexible-lower{background:#000}',
            'wpUpdateCustomCssPost' => true,
            'getOption' => '#site-header-flexible-upper{background:#fff}',
            'updateOption' => true,
        ]);

        $this->createMigration($wpService)->migrate();

        static::assertCount(0, $wpService->methodCalls['wpUpdateCustomCssPost'] ?? []);
        static::assertCount(0, $wpService->methodCalls['updateOption'] ?? []);
    }

    /**
     * Create migration instance.
     */
    private function createMigration(FakeWpService $wpService): object
    {
        $migrationClass = __NAMESPACE__ . '\\MigrateFlexibleHeaderContainerSelectors';

        return new $migrationClass($wpService);
    }
}
