<?php

declare(strict_types=1);

namespace Municipio\Upgrade\V57;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class MigrateFooterBeforeBackgroundToSiteWrapperAfterTest extends TestCase
{
    #[TestDox('migrate rewrites matching footer :before background rule in additional CSS')]
    public function testMigrateRewritesMatchingFooterBeforeBackgroundRuleInAdditionalCss(): void
    {
        $css = '.c-footer:before{content:"";background:url(https://cdn.example.org/footer-top.svg),#fff;}';

        $wpService = new FakeWpService([
            'wpGetCustomCss' => $css,
            'wpUpdateCustomCssPost' => static fn(string $css, array $args = []): \WP_Post|\WP_Error => new \WP_Error(),
            'getOption' => '',
            'updateOption' => true,
        ]);

        (new MigrateFooterBeforeBackgroundToSiteWrapperAfter($wpService))->migrate();

        static::assertSame(
            '.site-wrapper:after{content:"";background:url(https://cdn.example.org/footer-top.svg),#fff;}',
            $wpService->methodCalls['wpUpdateCustomCssPost'][0][0] ?? null,
        );
    }

    #[TestDox('migrate rewrites matching footer :before background rule in inline styles option')]
    public function testMigrateRewritesMatchingFooterBeforeBackgroundRuleInInlineStylesOption(): void
    {
        $optionCss = '.c-footer:before{content:"";background-image:url(/uploads/footer-top.svg);}';

        $wpService = new FakeWpService([
            'wpGetCustomCss' => '',
            'wpUpdateCustomCssPost' => true,
            'getOption' => static fn(string $option, mixed $default = false): mixed => $option === 'municipio_customizer_inline_styles' ? $optionCss : $default,
            'updateOption' => true,
        ]);

        (new MigrateFooterBeforeBackgroundToSiteWrapperAfter($wpService))->migrate();

        static::assertSame(
            ['municipio_customizer_inline_styles', '.site-wrapper:after{content:"";background-image:url(/uploads/footer-top.svg);}'],
            array_slice($wpService->methodCalls['updateOption'][0] ?? [], 0, 2),
        );
    }

    #[TestDox('migrate does not rewrite footer block without background image')]
    public function testMigrateDoesNotRewriteNonMatchingFooterBlock(): void
    {
        $css = '.c-footer:before{content:"";display:block;width:100%;background-size:cover;}';

        $wpService = new FakeWpService([
            'wpGetCustomCss' => $css,
            'wpUpdateCustomCssPost' => true,
            'getOption' => '',
            'updateOption' => true,
        ]);

        (new MigrateFooterBeforeBackgroundToSiteWrapperAfter($wpService))->migrate();

        static::assertCount(0, $wpService->methodCalls['wpUpdateCustomCssPost'] ?? []);
        static::assertCount(0, $wpService->methodCalls['updateOption'] ?? []);
    }
}
