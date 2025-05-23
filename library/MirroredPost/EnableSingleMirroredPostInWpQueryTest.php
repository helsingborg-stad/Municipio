<?php

namespace Municipio\MirroredPost;

use Municipio\MirroredPost\Contracts\BlogIdQueryVar;
use Municipio\MirroredPost\Utils\GetOtherBlogId\GetOtherBlogIdInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use WP_Query;
use WpService\Implementations\FakeWpService;

class EnableSingleMirroredPostInWpQueryTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    /**
     * @testdox class can be instantiated
     */
    public function testClassCanBeInstantiated(): void
    {
        $wpService = new FakeWpService();
        $instance  = new EnableSingleMirroredPostInWpQuery($wpService, $this->getUtils());

        $this->assertInstanceOf(
            EnableSingleMirroredPostInWpQuery::class,
            $instance
        );
    }

    /**
     * @testdox addHooks adds pre_get_posts action
     */
    public function testAddHooksAddsPreGetPostsAction(): void
    {
        $wpService = new FakeWpService(['addAction' => true]);
        $instance  = new EnableSingleMirroredPostInWpQuery($wpService, $this->getUtils());

        $instance->addHooks();

        $this->assertEquals(
            'pre_get_posts',
            $wpService->methodCalls['addAction'][0][0]
        );
    }

    /**
     * @testdox EnableSingleMirroredPostInWpQuery modifies the query correctly
     */
    public function testEnableSingleMirroredPostInWpQueryModifiesQueryCorrectly(): void
    {
        $wpService = new FakeWpService([
            'switchToBlog' => true,
            'getPostType'  => 'post',
            'isAdmin'      => false,
            'addFilter'    => true,
            'getQueryVar'  => fn($name, $default) => [BlogIdQueryVar::BLOG_ID_QUERY_VAR => 123, 'p' => 456][$name] ?? $default,
        ]);
        $utils     = $this->getUtils();
        $utils->method('getOtherBlogId')->willReturn(123);
        $instance = new EnableSingleMirroredPostInWpQuery($wpService, $utils);

        $query = $this->getWpQuery();
        $query->method('is_main_query')->willReturn(true);

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

    private function getUtils(): GetOtherBlogIdInterface|MockObject
    {
        return $this->createMock(GetOtherBlogIdInterface::class);
    }
}
