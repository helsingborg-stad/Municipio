<?php

declare(strict_types=1);

namespace Municipio\Customizer;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

/**
 * Tests design-center native font integration.
 */
class DesignCenterFontFamiliesTest extends TestCase
{
    #[TestDox('addHooks() registers the design-center font families filter')]
    public function testAddHooksRegistersTheDesignCenterFontFamiliesFilter(): void
    {
        $wpService = new FakeWpService([
            'addFilter' => true,
        ]);

        (new DesignCenterFontFamilies($wpService))->addHooks();

        static::assertContains(
            'Municipio/Styleguide/Customize/TokenData/FontFamilies',
            array_column($wpService->methodCalls['addFilter'], 0),
        );
    }

    #[TestDox('addStyleguideFontFamilies() appends installed native fonts and deduplicates values')]
    public function testAddStyleguideFontFamiliesAppendsInstalledNativeFontsAndDeduplicatesValues(): void
    {
        $wpService = new FakeWpService([
            'postTypeExists' => static fn(string $postType): bool => $postType === 'wp_font_family',
            'getPosts' => [
                (object) ['post_title' => 'Open Sans'],
                (object) ['post_title' => 'Merriweather'],
                (object) ['post_title' => 'Open Sans'],
            ],
        ]);

        $options = (new DesignCenterFontFamilies($wpService))->addStyleguideFontFamilies([
            ['value' => 'Arial, sans-serif', 'label' => 'Arial'],
            ['value' => '"Open Sans", sans-serif', 'label' => 'Open Sans'],
        ]);

        static::assertSame(
            [
                ['value' => 'Arial, sans-serif', 'label' => 'Arial'],
                ['value' => '"Open Sans", sans-serif', 'label' => 'Open Sans'],
                ['value' => '"Merriweather", sans-serif', 'label' => 'Merriweather'],
            ],
            $options,
        );
    }
}
