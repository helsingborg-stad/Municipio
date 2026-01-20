<?php

declare(strict_types=1);

namespace Municipio\PostsList\ConfigMapper;

use Municipio\PostsList\Config\AppearanceConfig\AppearanceConfigInterface;
use Municipio\PostsList\Config\FilterConfig\FilterConfigInterface;
use Municipio\PostsList\Config\GetPostsConfig\GetPostsConfigInterface;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class PostsListConfigDTOTest extends TestCase
{
    #[TestDox('tests PostsListConfigDTO properties and methods')]
    public function testPostsListConfigDTOPropertiesAndMethods(): void
    {
        $stubConfig = static::createMock(GetPostsConfigInterface::class);
        $stubAppearance = static::createMock(AppearanceConfigInterface::class);
        $stubFilter = static::createMock(FilterConfigInterface::class);
        $dto = new PostsListConfigDTO($stubConfig, $stubAppearance, $stubFilter, 'prefix_');

        static::assertSame($stubConfig, $dto->getGetPostsConfig());
        static::assertSame($stubAppearance, $dto->getAppearanceConfig());
        static::assertSame($stubFilter, $dto->getFilterConfig());
        static::assertSame('prefix_', $dto->getQueryVarsPrefix());
    }
}
