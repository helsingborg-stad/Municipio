<?php

declare(strict_types=1);

namespace Municipio\Integrations\Component;

use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class IconCustomizerTest extends TestCase
{
    public function testRegistersTheIconDataFilter(): void
    {
        $wpService = new FakeWpService(['addFilter' => true]);

        (new IconCustomizer($wpService))->addHooks();

        static::assertSame(
            'ComponentLibrary/Component/Icon/Data',
            $wpService->methodCalls['addFilter'][0][0],
        );
    }

    public function testAppliesSelectedCustomizerIconVariantAndWeight(): void
    {
        $wpService = new FakeWpService([
            'getThemeMod' => fn(string $setting): string => match ($setting) {
                'icon_style' => 'sharp',
                'icon_weight' => '600',
                default => '',
            },
        ]);

        $data = (new IconCustomizer($wpService))->applyIconSettings(['icon' => 'home']);

        static::assertSame('sharp', $data['variant']);
        static::assertSame(600, $data['weight']);
    }

    public function testFallsBackToComponentLibraryDefaultsForInvalidSettings(): void
    {
        $wpService = new FakeWpService([
            'getThemeMod' => fn(): string => 'invalid',
        ]);

        $data = (new IconCustomizer($wpService))->applyIconSettings(['icon' => 'home']);

        static::assertSame('outlined', $data['variant']);
        static::assertSame(400, $data['weight']);
    }
}
