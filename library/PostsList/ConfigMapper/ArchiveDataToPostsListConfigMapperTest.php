<?php

declare(strict_types=1);

namespace Municipio\PostsList\ConfigMapper;

use PHPUnit\Framework\TestCase;

class ArchiveDataToPostsListConfigMapperTest extends TestCase
{
    public function testMapReturnsConfigDTO(): void
    {
        $mapper = new ArchiveDataToPostsListConfigMapper();
        $fakeWpService = new class(['addFilter' => true]) extends \WpService\Implementations\FakeWpService {
            public function getPostTypeArchiveLink($postType): string|false
            {
                return '/archive/' . $postType;
            }
        };
        $fakeWpdb = new class('', '', '', '') extends \wpdb {};
        $data = [
            'queryVarsPrefix' => 'archive_',
            'wpTaxonomies' => [],
            'wpService' => $fakeWpService,
            'wpdb' => $fakeWpdb,
            // Minimal valid keys for factories
            'postType' => 'post',
            'customizer' => (object) [],
            'archiveProps' => (object) [],
        ];
        $dto = $mapper->map($data);
        $this->assertInstanceOf(PostsListConfigDTO::class, $dto);
        $this->assertInstanceOf(
            \Municipio\PostsList\Config\GetPostsConfig\GetPostsConfigInterface::class,
            $dto->getPostsConfig,
        );
        $this->assertInstanceOf(
            \Municipio\PostsList\Config\AppearanceConfig\AppearanceConfigInterface::class,
            $dto->appearanceConfig,
        );
        $this->assertInstanceOf(
            \Municipio\PostsList\Config\FilterConfig\FilterConfigInterface::class,
            $dto->filterConfig,
        );
        $this->assertEquals('archive_', $dto->queryVarsPrefix);
    }
}
