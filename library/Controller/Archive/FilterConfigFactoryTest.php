<?php

namespace Municipio\Controller\Archive;

use Municipio\PostsList\Config\FilterConfig\FilterConfigInterface;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class FilterConfigFactoryTest extends TestCase
{
    #[TestDox('create returns an instance of FilterConfigInterface')]
    public function testCreateReturnsFilterConfigInterface(): void
    {
        define('ARRAY_A', 'ARRAY_A'); // Mock constant for testing
        $factory = new FilterConfigFactory(
            ['archiveProps' => (object) []],
            [],
            new FakeWpService([
                'homeUrl' => 'https://example.com',
                'getPostTypeArchiveLink' => 'https://example.com/page',
                'getQueriedObject' => null,
                'getTermLink' => 'https://example.com/term',
            ]),
            $this->createMock(\Municipio\PostsList\QueryVars\QueryVarsInterface::class),
        );

        $result = $factory->create(['archiveProps' => (object) []]);

        $this->assertInstanceOf(FilterConfigInterface::class, $result);
    }
}
