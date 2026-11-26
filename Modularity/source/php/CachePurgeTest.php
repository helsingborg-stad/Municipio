<?php

declare(strict_types=1);

namespace Modularity;

use Modularity\Helper\FragmentCache\Fragment;
use Modularity\Helper\FragmentCache\FragmentStore;
use Municipio\TestUtils\InMemoryObjectCache;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Contracts\AddAction;
use WpService\Contracts\GetPosts;
use WpService\Contracts\GetPostType;
use WpService\Contracts\GetThePermalink;
use WpService\Contracts\WpIsPostRevision;
use WpService\Contracts\WpRemoteRequest;

class CachePurgeTest extends TestCase
{
    private FragmentStore $fragmentStore;

    protected function setUp(): void
    {
        $this->fragmentStore = new FragmentStore(new InMemoryObjectCache(), 'modules');
    }

    #[TestDox('purgeObjectCache() clears the fragments of Posts modules that list the saved post type')]
    public function testPurgeObjectCacheClearsFragmentsOfPostsModulesListingThePostType(): void
    {
        $this->fragmentStore->set(77, 'hash', new Fragment('<p>List of news</p>', []), 60);
        $cachePurge = new CachePurge($this->fragmentStore, $this->createWpService(postType: 'news', postsModuleIds: [77]));

        static::assertTrue($cachePurge->purgeObjectCache(10));
        static::assertNull($this->fragmentStore->get(77, 'hash'));
    }

    #[TestDox('purgeObjectCache() leaves fragments untouched when a revision is saved')]
    public function testPurgeObjectCacheIgnoresRevisions(): void
    {
        $this->fragmentStore->set(77, 'hash', new Fragment('<p>List of news</p>', []), 60);
        $cachePurge = new CachePurge(
            $this->fragmentStore,
            $this->createWpService(postType: 'news', postsModuleIds: [77], isRevision: true),
        );

        static::assertFalse($cachePurge->purgeObjectCache(10));
        static::assertNotNull($this->fragmentStore->get(77, 'hash'));
    }

    // @mago-expect lint:no-boolean-flag-parameter
    /**
     * @param list<int> $postsModuleIds
     */
    private function createWpService(
        string $postType,
        array $postsModuleIds,
        bool $isRevision = false,
    ): AddAction&WpIsPostRevision&GetPostType&GetPosts&GetThePermalink&WpRemoteRequest {
        return new class($postType, $postsModuleIds, $isRevision) implements AddAction, WpIsPostRevision, GetPostType, GetPosts, GetThePermalink, WpRemoteRequest {
            /**
             * @param list<int> $postsModuleIds
             */
            public function __construct(
                private readonly string $postType,
                private readonly array $postsModuleIds,
                private readonly bool $isRevision,
            ) {}

            public function addAction(string $hookName, callable $callback, int $priority = 10, int $acceptedArgs = 1): true
            {
                return true;
            }

            public function wpIsPostRevision(int|\WP_Post $post): int|false
            {
                return $this->isRevision ? 1 : false;
            }

            public function getPostType(int|\WP_Post|null $post = null): string|false
            {
                return $this->postType;
            }

            public function getPosts(?array $args = null): array
            {
                return array_map(static function (int $id): \WP_Post {
                    $post = new \WP_Post((object) []);
                    $post->ID = $id;

                    return $post;
                }, $this->postsModuleIds);
            }

            // @mago-expect lint:no-boolean-flag-parameter
            public function getThePermalink(int|\WP_Post $post = 0, bool $leavename = false): string|false
            {
                return false;
            }

            public function wpRemoteRequest(string $url, array $args = []): array|\WP_Error
            {
                return [];
            }
        };
    }
}
