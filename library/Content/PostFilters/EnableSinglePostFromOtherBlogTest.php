<?php

namespace Municipio\Content\PostFilters;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use WP_Query;
use WpService\Implementations\FakeWpService;

class EnableSinglePostFromOtherBlogTest extends TestCase
{
    private FakeWpService $wpService;
    private EnableSinglePostFromOtherBlog $instance;

    protected function setUp(): void
    {
        parent::setUp();
        $this->wpService = new FakeWpService();
        $this->instance  = new EnableSinglePostFromOtherBlog($this->wpService);
    }

    /**
     * @testdox class can be instantiated
     */
    public function testClassCanBeInstantiated(): void
    {
        $this->assertInstanceOf(
            EnableSinglePostFromOtherBlog::class,
            $this->instance
        );
    }

    /**
     * @testdox addHooks adds pre_get_posts action
     */
    public function testAddHooksAddsPreGetPostsAction(): void
    {
        $wpService = new FakeWpService(['addAction' => true]);
        $instance  = new EnableSinglePostFromOtherBlog($wpService);

        $instance->addHooks();

        $this->assertEquals(
            'pre_get_posts',
            $wpService->methodCalls['addAction'][0][0]
        );
    }

    /**
     * @testdox enableSinglePostFromOtherBlog modifies the query correctly
     */
    public function testEnableSinglePostFromOtherBlogModifiesQueryCorrectly(): void
    {
        $instance = new EnableSinglePostFromOtherBlog(new FakeWpService([
            'switchToBlog'     => true,
            'getPostType'      => 'post',
            'isAdmin'          => false,
            'isMultisite'      => true,
            'msIsSwitched'     => false,
            'getCurrentBlogId' => 1,
            'addFilter'        => true
        ]));

        $query = $this->getWpQuery();
        $query->method('is_main_query')->willReturn(true);

        $_GET['blog_id'] = 123;
        $_GET['p']       = 456;

        $query->expects($this->once())
            ->method('set')
            ->with('post_type', 'post');

        $instance->handlePreGetPosts($query);
    }

    /**
     * Creates and returns a mock instance of the WP_Query class.
     *
     * @return WP_Query|MockObject A mock object for WP_Query, used for testing purposes.
     */
    private function getWpQuery(): WP_Query|MockObject
    {
        return $this->createMock(WP_Query::class);
    }
}
