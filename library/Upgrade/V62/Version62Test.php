<?php

declare(strict_types=1);

namespace Municipio\Upgrade\V62;

use Municipio\Upgrade\VersionInterface;
use Override;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Contracts\GetPosts;
use WpService\Contracts\GetPostTypes;
use WpService\Contracts\UpdatePostMeta;

class Version62Test extends TestCase {
    #[TestDox('migrates "exclude_from_search" to "exclude_local_search"')]
    public function testMigration(): void {
        $wpService = new class implements GetPosts, GetPostTypes, UpdatePostMeta {
            public array $updatedPostMeta = [];
            public ?array $postQueryArgs = null;

            public function getPostTypes(?array $args = null): array
            {
                return ['post', 'page', 'attachment'];
            }

            public function getPosts(?array $args = null): array
            {
                $this->postQueryArgs = $args;
                return [1, 2, 3]; // Example post IDs for testing purposes
            }
            
            public function updatePostMeta(int $postId, string $metaKey, mixed $metaValue, mixed $prevValue = ''): int|bool
            {
                $this->updatedPostMeta[] = [
                    'postId' => $postId,
                    'metaKey' => $metaKey,
                    'metaValue' => $metaValue,
                ];
                return true;
            }
        };

        $version62 = new Version62($wpService);
        $version62->upgradeToVersion();

        static::assertSame(['post', 'page', 'attachment'], $wpService->postQueryArgs['post_type']);
        static::assertSame('any', $wpService->postQueryArgs['post_status']);
        static::assertSame('exclude_from_search', $wpService->postQueryArgs['meta_key']);
        static::assertSame('1', $wpService->postQueryArgs['meta_value']);
        static::assertSame(-1, $wpService->postQueryArgs['posts_per_page']);
        static::assertSame('ids', $wpService->postQueryArgs['fields']);
        static::assertCount(3, $wpService->updatedPostMeta);
        static::assertEquals('exclude_local_search', $wpService->updatedPostMeta[0]['metaKey']);
        static::assertEquals('exclude_local_search', $wpService->updatedPostMeta[1]['metaKey']);
        static::assertEquals('exclude_local_search', $wpService->updatedPostMeta[2]['metaKey']);
        static::assertEquals(1, $wpService->updatedPostMeta[0]['postId']);
        static::assertEquals(2, $wpService->updatedPostMeta[1]['postId']);
        static::assertEquals(3, $wpService->updatedPostMeta[2]['postId']);
        static::assertEquals('1', $wpService->updatedPostMeta[0]['metaValue']);
        static::assertEquals('1', $wpService->updatedPostMeta[1]['metaValue']);
        static::assertEquals('1', $wpService->updatedPostMeta[2]['metaValue']);
    }
}