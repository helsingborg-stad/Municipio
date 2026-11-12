<?php

declare(strict_types=1);

namespace Municipio\ImagePreload;

use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class LqipPreloadConfigTest extends TestCase
{
    public function testExposesFilterablePreloadOptions(): void
    {
        $config = new LqipPreloadConfig(new FakeWpService([
            'applyFilters' => static fn(string $filter, mixed $value): mixed => match ($filter) {
                'Municipio/ImagePreload/IsEnabled' => false,
                'Municipio/ImagePreload/MaxPreloads' => 4,
                'Municipio/ImagePreload/FetchPriority' => 'low',
                default => $value,
            },
        ]));

        static::assertFalse($config->isEnabled());
        static::assertSame(4, $config->maxPreloads());
        static::assertSame('low', $config->fetchPriority());
    }

    public function testThrowsForAnInvalidFetchPriority(): void
    {
        $config = new LqipPreloadConfig(new FakeWpService([
            'applyFilters' => static fn(): string => 'invalid',
        ]));

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid LQIP preload fetch priority');

        $config->fetchPriority();
    }
}
