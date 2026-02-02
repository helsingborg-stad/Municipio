<?php

declare(strict_types=1);

namespace Municipio\Controller\Archive;

use PHPUnit\Framework\TestCase;
use Municipio\PostsList\Config\AppearanceConfig\AppearanceConfigInterface;
use Municipio\PostsList\Config\GetPostsConfig\GetPostsConfigInterface;

class AsyncConfigBuilderFactoryTest extends TestCase
{
    public function testCreateReturnsExpectedArray(): void
    {
        // Mock appearance config
        $appearanceConfig = $this->createMock(AppearanceConfigInterface::class);
        $appearanceConfig->method('getDateSource')->willReturn('post_date');
        $appearanceConfig->method('getDateFormat')->willReturn((object)['value' => 'date-time']);
        $appearanceConfig->method('getNumberOfColumns')->willReturn(2);

        // Mock get posts config
        $getPostsConfig = $this->createMock(GetPostsConfigInterface::class);
        $getPostsConfig->method('getPostTypes')->willReturn(['post']);
        $getPostsConfig->method('getPostsPerPage')->willReturn(5);
        $getPostsConfig->method('paginationEnabled')->willReturn(true);

        // Mock posts list config DTO
        $postsListConfigDTO = new class($appearanceConfig, $getPostsConfig) {
            public function __construct(
                private $appearanceConfig,
                private $getPostsConfig
            ) {}

            public function getAppearanceConfig() {
                return $this->appearanceConfig;
            }

            public function getGetPostsConfig() {
                return $this->getPostsConfig;
            }

            public function getQueryVarsPrefix() {
                return 'archive_';
            }
        };

        $postsListData = ['id' => 'archive_id'];

        // Create factory with builder
        $builder = new AsyncConfigBuilder();
        $factory = new AsyncConfigBuilderFactory($builder);

        $result = $factory->create($postsListConfigDTO, $postsListData, true);

        $this->assertEquals([
            'queryVarsPrefix' => 'archive_',
            'id' => 'archive_id',
            'postType' => 'post',
            'dateSource' => 'post_date',
            'dateFormat' => 'date-time',
            'numberOfColumns' => 2,
            'postsPerPage' => 5,
            'paginationEnabled' => true,
            'asyncId' => 'archive_id',
            'isAsync' => true,
        ], $result);
    }

    public function testFromConfigsStaticMethodForBackwardCompatibility(): void
    {
        // Mock appearance config
        $appearanceConfig = $this->createMock(AppearanceConfigInterface::class);
        $appearanceConfig->method('getDateSource')->willReturn('modified_date');
        $appearanceConfig->method('getDateFormat')->willReturn((object)['value' => 'date']);
        $appearanceConfig->method('getNumberOfColumns')->willReturn(3);

        // Mock get posts config
        $getPostsConfig = $this->createMock(GetPostsConfigInterface::class);
        $getPostsConfig->method('getPostTypes')->willReturn(['page']);
        $getPostsConfig->method('getPostsPerPage')->willReturn(15);
        $getPostsConfig->method('paginationEnabled')->willReturn(false);

        // Mock posts list config DTO
        $postsListConfigDTO = new class($appearanceConfig, $getPostsConfig) {
            public function __construct(
                private $appearanceConfig,
                private $getPostsConfig
            ) {}

            public function getAppearanceConfig() {
                return $this->appearanceConfig;
            }

            public function getGetPostsConfig() {
                return $this->getPostsConfig;
            }

            public function getQueryVarsPrefix() {
                return 'custom_';
            }
        };

        $postsListData = ['id' => 'custom_id'];

        $result = AsyncConfigBuilderFactory::fromConfigs(
            $postsListConfigDTO,
            $postsListData,
            false
        );

        $this->assertEquals([
            'queryVarsPrefix' => 'custom_',
            'id' => 'custom_id',
            'postType' => 'page',
            'dateSource' => 'modified_date',
            'dateFormat' => 'date',
            'numberOfColumns' => 3,
            'postsPerPage' => 15,
            'paginationEnabled' => false,
            'asyncId' => 'custom_id',
            'isAsync' => false,
        ], $result);
    }

    public function testFactoryReusesBuilderCorrectly(): void
    {
        $appearanceConfig = $this->createMock(AppearanceConfigInterface::class);
        $getPostsConfig = $this->createMock(GetPostsConfigInterface::class);
        $getPostsConfig->method('getPostTypes')->willReturn(['post']);

        $postsListConfigDTO = new class($appearanceConfig, $getPostsConfig) {
            public function __construct(
                private $appearanceConfig,
                private $getPostsConfig
            ) {}

            public function getAppearanceConfig() {
                return $this->appearanceConfig;
            }

            public function getGetPostsConfig() {
                return $this->getPostsConfig;
            }

            public function getQueryVarsPrefix() {
                return 'test_';
            }
        };

        $builder = new AsyncConfigBuilder();
        $factory = new AsyncConfigBuilderFactory($builder);

        // First call
        $result1 = $factory->create($postsListConfigDTO, ['id' => 'first'], true);

        // Second call - builder should be reset
        $result2 = $factory->create($postsListConfigDTO, ['id' => 'second'], false);

        $this->assertEquals('first', $result1['id']);
        $this->assertTrue($result1['isAsync']);

        $this->assertEquals('second', $result2['id']);
        $this->assertFalse($result2['isAsync']);
    }
}
