<?php

declare(strict_types=1);

namespace Municipio\Upgrade\V62;

use Municipio\Upgrade\VersionInterface;
use Override;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Contracts\GetPosts;
use WpService\Contracts\UpdatePostMeta;

class Version62Test extends TestCase {
    #[TestDox('migrates "exclude_from_search" to "exclude_local_search"')]
    public function testMigration(): void {
        $wpService = new class implements GetPosts, UpdatePostMeta {
            public array $updatedPostMeta = [];
            public function getPosts(?array $args = null): array
            {
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