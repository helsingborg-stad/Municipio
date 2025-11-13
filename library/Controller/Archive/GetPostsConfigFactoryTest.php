<?php

namespace Municipio\Controller\Archive;

use Municipio\PostsList\Config\GetPostsConfig\GetPostsConfigInterface;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Contracts\GetThemeMod;

class FakeWpService implements GetThemeMod
{
    public function getThemeMod(string $name, mixed $defaultValue = false): mixed
    {
        return $defaultValue;
    }
}

class GetPostsConfigFactoryTest extends TestCase
{
    #[TestDox('create returns an instance of GetPostsConfigInterface')]
    public function testCreateReturnsGetPostsConfigInterface(): void
    {
        $factory = new GetPostsConfigFactory(new FakeWpService());

        $result = $factory->create(['archiveProps' => (object) []]);

        $this->assertInstanceOf(GetPostsConfigInterface::class, $result);
    }
}
