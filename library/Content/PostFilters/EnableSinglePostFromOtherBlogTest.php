<?php

namespace Municipio\Content\PostFilters;

use PHPUnit\Framework\TestCase;
use WP_Query;
use WpService\Implementations\FakeWpService;

class EnableSinglePostFromOtherBlogTest extends TestCase
{
    /**
     * @testdox class can be instantiated
     */
    public function testClassCanBeInstantiated(): void
    {
        $instance = new EnableSinglePostFromOtherBlog(new FakeWpService());

        $this->assertInstanceOf(EnableSinglePostFromOtherBlog::class, $instance);
    }

    /**
     * @testdox addHooks adds pre_get_posts action
     */
    public function testAddHooksAddsPreGetPostsAction(): void
    {
        $wpService = new FakeWpService(['addAction' => true]);
        $instance  = new EnableSinglePostFromOtherBlog($wpService);

        $instance->addHooks();

        $this->assertEquals('pre_get_posts', $wpService->methodCalls['addAction'][0][0]);
    }

    /**
     * @testdox enableSinglePostFromOtherBlog modifies the query correctly
     */
    public function testEnableSinglePostFromOtherBlogModifiesQueryCorrectly(): void
    {
        $wpService = new FakeWpService([
            'switchToBlog'     => true,
            'getPostType'      => 'post',
            'isAdmin'          => false,
            'isMultisite'      => true,
            'msIsSwitched'     => false,
            'getCurrentBlogId' => 1,
            'addFilter'        => true
        ]);
        $instance  = new EnableSinglePostFromOtherBlog($wpService);

        // Simulate a query object
        $query = $this->createMock(WP_Query::class);
        $query->method('is_main_query')->willReturn(true);
        $_GET['blog_id'] = 123;
        $_GET['p']       = 456;

        $query->expects($this->once())->method('set')->with('post_type', 'post');

        $instance->handlePreGetPosts($query);
    }
}
