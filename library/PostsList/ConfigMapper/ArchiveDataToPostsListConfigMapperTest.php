<?php

declare(strict_types=1);

namespace Municipio\PostsList\ConfigMapper;

use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class ArchiveDataToPostsListConfigMapperTest extends TestCase
{
    public function testMapReturnsConfigDTO(): void
    {
        $mapper = new ArchiveDataToPostsListConfigMapper();
        $wpService = new FakeWpService([
            'homeUrl' => 'https://example.com',
            'getPostTypeArchiveLink' => function ($postType) {
                return '/archive/' . $postType;
            },
            '__' => function ($msg, $domain = null) {
                return $msg;
            },
            'wpAdminNotice' => function ($msg, $opts = []) {
                return null;
            },
        ]);
        $fakeWpdb = new class('', '', '', '') extends \wpdb {};
        $data = [
            'queryVarsPrefix' => 'archive_',
            'wpTaxonomies' => [],
            'wpService' => $wpService,
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
