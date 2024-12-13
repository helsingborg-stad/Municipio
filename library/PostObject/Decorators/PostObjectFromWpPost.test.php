<?php

namespace Municipio\PostObject\Decorators;

use Municipio\PostObject\PostObject;
use Municipio\TestUtils\WpMockFactory;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class PostObjectFromWpPostTest extends TestCase
{
    /**
     * @testdox Get comment count returns amount of comments
     */
    public function testGetCommentCountReturnsAmountOfComments()
    {
        $wpService = new FakeWpService(['getCommentCount' => ['approved' => 2]]);
        $wpPost    = WpMockFactory::createWpPost(['ID' => 1]);

        $instance = new PostObjectFromWpPost(new PostObject(), $wpPost, $wpService);

        $result = $instance->getCommentCount();

        $this->assertEquals(2, $result);
    }

    /**
     * @testdox getPostType returns post type
     */
    public function testGetPostTypeReturnsPostType()
    {
        $wpPost   = WpMockFactory::createWpPost(['post_type' => 'post']);
        $instance = new PostObjectFromWpPost(new PostObject(), $wpPost, new FakeWpService());

        $this->assertEquals('post', $instance->getPostType());
    }
}
