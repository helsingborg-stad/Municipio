<?php

declare(strict_types=1);

namespace Municipio\SearchIndex\Index;

use Municipio\SearchIndex\Provider\SearchProviderInterface;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class PostIndexerTest extends TestCase
{
    public function testRegistersLifecycleHooksForInheritedPageData(): void
    {
        $wpService = new FakeWpService(['addAction' => true]);
        $indexer = new PostIndexer($wpService, $this->createStub(SearchProviderInterface::class));

        $indexer->addHooks();

        $hooks = $wpService->methodCalls['addAction'];
        static::assertSame('wp_after_insert_post', $hooks[0][0]);
        static::assertSame([$indexer, 'indexAfterInsert'], $hooks[0][1]);
        static::assertSame(4, $hooks[0][3]);
        static::assertContains('before_delete_post', array_column($hooks, 0));
        static::assertContains('deleted_post', array_column($hooks, 0));
    }

    public function testReindexesDescendantsAndAttachmentsWhenAnAncestorChanges(): void
    {
        $child = $this->post(2, 'page', 1);
        $attachment = $this->post(3, 'attachment', 1);
        $wpService = new FakeWpService([
            'getPages' => [$child],
            'getPosts' => static fn(array $query): array => $query['post_parent'] === 1 ? [$attachment] : [],
            'getPostType' => static fn(\WP_Post $post): string => $post->post_type,
        ]);
        $indexer = new class ($wpService, $this->createStub(SearchProviderInterface::class)) extends PostIndexer {
            /** @var array<int, int> */
            public array $indexed = [];

            public function index(int|\WP_Post $post): void
            {
                $this->indexed[] = $post instanceof \WP_Post ? $post->ID : $post;
            }
        };
        $before = $this->post(1, 'page');
        $before->post_name = 'old-name';
        $after = $this->post(1, 'page');
        $after->post_name = 'new-name';

        $indexer->indexAfterInsert(1, $after, true, $before);

        static::assertSame([1], $indexer->indexed);
        static::assertSame([
            ['Municipio/SearchIndex/IndexPostId', 3],
            ['Municipio/SearchIndex/IndexPostId', 2],
        ], $wpService->methodCalls['doAction']);
        static::assertSame(['child_of' => 1, 'post_status' => 'publish'], $wpService->methodCalls['getPages'][0][0]);
    }

    public function testReindexesRememberedDescendantsAfterPageDeletion(): void
    {
        $page = $this->post(1, 'page');
        $child = $this->post(2, 'page', 1);
        $wpService = new FakeWpService([
            'getPost' => $page,
            'getPostType' => 'page',
            'getPages' => [$child],
            'getPosts' => [],
        ]);
        $indexer = new PostIndexer($wpService, $this->createStub(SearchProviderInterface::class));

        $indexer->rememberDescendantsOfDeletedPage(1);
        $indexer->reindexDescendantsOfDeletedPage(1);

        static::assertSame([
            ['Municipio/SearchIndex/IndexPostId', 2],
        ], $wpService->methodCalls['doAction']);
    }

    private function post(int $id, string $type, int $parent = 0): \WP_Post
    {
        $post = new \WP_Post((object) []);
        $post->ID = $id;
        $post->post_type = $type;
        $post->post_parent = $parent;
        $post->post_name = 'page-' . $id;
        $post->post_title = 'Page ' . $id;

        return $post;
    }
}
