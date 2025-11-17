<?php

namespace Municipio\Controller\Archive;

use Municipio\PostsList\Config\FilterConfig\FilterConfigInterface;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\WpService;

class FilterConfigFactoryTest extends TestCase
{
    #[TestDox('create returns an instance of FilterConfigInterface')]
    public function testCreateReturnsFilterConfigInterface(): void
    {
        define('ARRAY_A', 'ARRAY_A'); // Mock constant for testing
        $factory = new FilterConfigFactory(['archiveProps' => (object)[]], [], $this->createMock(WpService::class));

        $result = $factory->create(['archiveProps' => (object)[]]);

        $this->assertInstanceOf(FilterConfigInterface::class, $result);
    }
}
