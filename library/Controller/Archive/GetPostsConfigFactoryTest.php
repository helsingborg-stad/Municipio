<?php

namespace Municipio\Controller\Archive;

use Municipio\PostsList\Config\FilterConfig\FilterConfigInterface;
use Municipio\PostsList\Config\GetPostsConfig\GetPostsConfigInterface;
use Municipio\PostsList\QueryVars\QueryVarsInterface;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WP_Error;
use WpService\Contracts\GetTerms;
use WpService\Contracts\GetThemeMod;

class FakeWpService implements GetThemeMod, GetTerms
{
    public function getThemeMod(string $name, mixed $defaultValue = false): mixed
    {
        return $defaultValue;
    }

    public function getTerms(array|string $args = [], array|string $deprecated = ''): array|string|WP_Error
    {
        return [];
    }
}

class GetPostsConfigFactoryTest extends TestCase
{
    #[TestDox('create returns an instance of GetPostsConfigInterface')]
    public function testCreateReturnsGetPostsConfigInterface(): void
    {
        $factory = new GetPostsConfigFactory(
            $this->createMock(FilterConfigInterface::class),
            $this->createMock(QueryVarsInterface::class),
            new FakeWpService()
        );

        $result = $factory->create(['archiveProps' => (object) []]);

        $this->assertInstanceOf(GetPostsConfigInterface::class, $result);
    }
}
