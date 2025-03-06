<?php

namespace Municipio\ExternalContent\SyncHandler\Cleanup;

use Municipio\ExternalContent\SyncHandler\SyncHandler;
use PHPUnit\Framework\TestCase;
use Spatie\SchemaOrg\Schema;
use WP_Post;
use WpService\Implementations\FakeWpService;

class CleanupPostsNoLongerInSourceTest extends TestCase
{
    /**
     * @testdox class can be instantiated
     */
    public function testClassCanBeInstantiated()
    {
        $cleanup = new CleanupPostsNoLongerInSource('post', new FakeWpService());
        $this->assertInstanceOf(CleanupPostsNoLongerInSource::class, $cleanup);
    }

    /**
     * @testdox addHook adds a hook for the cleanup method
     */
    public function testAddHookAddsHookForCleanupMethod()
    {
        $wpService = new FakeWpService(['addAction' => true]);
        $cleanup   = new CleanupPostsNoLongerInSource('post', $wpService);

        $cleanup->addHooks();

        $this->assertEquals(SyncHandler::ACTION_AFTER, $wpService->methodCalls['addAction'][0][0]);
        $this->assertEquals([$cleanup, 'cleanup'], $wpService->methodCalls['addAction'][0][1]);
    }

    /**
     * @testdox calls getPosts with correct arguments
     */
    public function testCallsGetPostsWithCorrectArguments()
    {
        $wpService = new FakeWpService(['getPosts' => []]);
        $cleanup   = new CleanupPostsNoLongerInSource('test_post_type', $wpService);

        $cleanup->cleanup([Schema::thing()->setProperty('@id', '1')]);

        $this->assertEquals('test_post_type', $wpService->methodCalls['getPosts'][0][0]['post_type']);
        $this->assertEquals('originId', $wpService->methodCalls['getPosts'][0][0]['meta_key']);
        $this->assertEquals(['1'], $wpService->methodCalls['getPosts'][0][0]['meta_value']);
    }

    /**
     * @testdox calls wpDeletePost for each post that is no longer in the source
     */
    public function testCallsWpDeletePostForEachPostThatIsNoLongerInTheSource()
    {
        $post             = new WP_Post([]);
        $post->ID         = 2;
        $postsToBeDeleted = [$post];
        $wpService        = new FakeWpService(['getPosts' => $postsToBeDeleted, 'wpDeletePost' => new WP_Post([])]);
        $cleanup          = new CleanupPostsNoLongerInSource('post', $wpService);

        $cleanup->cleanup([Schema::thing()->setProperty('@id', '1')]);

        $this->assertCount(1, $wpService->methodCalls['wpDeletePost']);
        $this->assertEquals(2, $wpService->methodCalls['wpDeletePost'][0][0]);
    }

    /**
     * @testdox does not call wpDeletePost if no posts found that are no longer in the source
     */
    public function testDoesNotCallWpDeletePostIfNoPostsFoundThatAreNoLongerInTheSource()
    {
        $wpService = new FakeWpService(['getPosts' => []]);
        $cleanup   = new CleanupPostsNoLongerInSource('post', $wpService);

        $cleanup->cleanup([Schema::thing()->setProperty('@id', '1')]);

        $this->assertArrayNotHasKey('wpDeletePost', $wpService->methodCalls);
    }
}
