<?php

declare(strict_types=1);

namespace Municipio\ImagePreload;

use ComponentLibrary\Integrations\Image\ImageInterface;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class LqipPreloadCollectorTest extends TestCase
{
    protected function tearDown(): void
    {
        LqipPreloadRegistry::reset();
    }

    public function testRegistersTheImageDataFilter(): void
    {
        $wpService = new FakeWpService(['addFilter' => true]);

        (new LqipPreloadCollector($wpService, $this->createConfig()))->addHooks();

        static::assertSame(
            'ComponentLibrary/Component/Image/Data',
            $wpService->methodCalls['addFilter'][0][0],
        );
    }

    public function testCollectsEachEnabledLqipOnlyOnce(): void
    {
        $image = $this->createMock(ImageInterface::class);
        $image->method('getLqipUrl')->willReturn('https://example.com/image-15x8.webp');
        $collector = new LqipPreloadCollector(new FakeWpService(), $this->createConfig());

        $collector->collect(['src' => $image, 'lqipEnabled' => true]);
        $collector->collect(['src' => $image, 'lqipEnabled' => true]);

        static::assertSame(['https://example.com/image-15x8.webp'], LqipPreloadRegistry::all());
    }

    public function testSkipsDisabledLqips(): void
    {
        $image = $this->createMock(ImageInterface::class);
        $image->expects(static::never())->method('getLqipUrl');

        (new LqipPreloadCollector(new FakeWpService(), $this->createConfig()))->collect([
            'src' => $image,
            'lqipEnabled' => false,
        ]);

        static::assertSame([], LqipPreloadRegistry::all());
    }

    public function testLimitsPreloadsToTheFirstTenLqipsInLoadOrder(): void
    {
        for ($index = 1; $index <= 11; $index++) {
            LqipPreloadRegistry::add(sprintf('https://example.com/image-%d-15x8.webp', $index));
        }

        static::assertSame(
            array_map(
                static fn(int $index): string => sprintf('https://example.com/image-%d-15x8.webp', $index),
                range(1, 10),
            ),
            LqipPreloadRegistry::all(),
        );
    }

    public function testUsesTheConfiguredPreloadLimitAndPriority(): void
    {
        $config = $this->createStub(LqipPreloadConfigInterface::class);
        $config->method('isEnabled')->willReturn(true);
        $config->method('maxPreloads')->willReturn(2);
        $config->method('fetchPriority')->willReturn('low');
        LqipPreloadRegistry::configure($config);

        LqipPreloadRegistry::add('https://example.com/image-1-15x8.webp');
        LqipPreloadRegistry::add('https://example.com/image-2-15x8.webp');
        LqipPreloadRegistry::add('https://example.com/image-3-15x8.webp');

        static::assertSame([
            'https://example.com/image-1-15x8.webp',
            'https://example.com/image-2-15x8.webp',
        ], LqipPreloadRegistry::all());
        static::assertSame('low', LqipPreloadRegistry::fetchPriority());
    }

    private function createConfig(): LqipPreloadConfigInterface
    {
        $config = $this->createStub(LqipPreloadConfigInterface::class);
        $config->method('isEnabled')->willReturn(true);
        $config->method('maxPreloads')->willReturn(10);
        $config->method('fetchPriority')->willReturn('high');

        return $config;
    }
}
