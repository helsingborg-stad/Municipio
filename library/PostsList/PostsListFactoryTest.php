<?php

declare(strict_types=1);

namespace Municipio\PostsList;

use Municipio\PostsList\ConfigMapper\PostsListConfigDTOInterface;
use Municipio\SchemaData\Utils\SchemaToPostTypesResolver\SchemaToPostTypeResolverInterface;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class PostsListFactoryTest extends TestCase
{
    #[TestDox('creates PostsList instance')]
    public function testCreatePostsListInstance(): void
    {
        $wpService = new FakeWpService(['addFilter' => true]);
        $wpdb = new class('', '', '', '') extends \wpdb {};
        $schemaResolver = new class implements SchemaToPostTypeResolverInterface {
            public function resolve(string $schemaType): array
            {
                return [];
            }
        };
        $factory = new PostsListFactory($wpService, $wpdb, $schemaResolver);
        $postsList = $factory->create($this->createMock(PostsListConfigDTOInterface::class));

        static::assertInstanceOf(PostsList::class, $postsList);
    }
}
