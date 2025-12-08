<?php

declare(strict_types=1);

namespace Municipio\PostsList\ConfigMapper;

use Municipio\PostsList\Config\AppearanceConfig\AppearanceConfigInterface;
use Municipio\PostsList\Config\FilterConfig\FilterConfigInterface;
use Municipio\PostsList\Config\GetPostsConfig\GetPostsConfigInterface;
use PHPUnit\Framework\TestCase;

class PostsListConfigDTOTest extends TestCase
{
    public function testDTOProperties(): void
    {
        $stubConfig = $this->createMock(GetPostsConfigInterface::class);
        $stubAppearance = $this->createMock(AppearanceConfigInterface::class);
        $stubFilter = $this->createMock(FilterConfigInterface::class);
        $dto = new PostsListConfigDTO($stubConfig, $stubAppearance, $stubFilter, 'prefix_');
        $this->assertSame($stubConfig, $dto->getPostsConfig);
        $this->assertSame($stubAppearance, $dto->appearanceConfig);
        $this->assertSame($stubFilter, $dto->filterConfig);
        $this->assertEquals('prefix_', $dto->queryVarsPrefix);
    }
}
