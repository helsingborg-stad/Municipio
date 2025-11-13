<?php

namespace Municipio\Controller\Archive;

use Municipio\PostsList\Config\FilterConfig\FilterConfigInterface;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Contracts\GetTaxonomies;

class FilterConfigFactoryTest extends TestCase
{
    #[TestDox('create returns an instance of FilterConfigInterface')]
    public function testCreateReturnsFilterConfigInterface(): void
    {
        $factory = new FilterConfigFactory($this->createMock(GetTaxonomies::class));

        $result = $factory->create(['archiveProps' => (object)[]]);

        $this->assertInstanceOf(FilterConfigInterface::class, $result);
    }
}
