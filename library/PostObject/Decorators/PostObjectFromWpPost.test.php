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
     * @testdox getPermalink() returns permalink
     */
    public function testGetPermalinkReturnsPermalink()
    {
        $wpService = new FakeWpService(['getPermalink' => 'http://example.com']);
        $wpPost    = WpMockFactory::createWpPost(['ID' => 1]);

        $instance = new PostObjectFromWpPost(new PostObject(), $wpPost, $wpService);

        $result = $instance->getPermalink();

        $this->assertEquals('http://example.com', $result);
    }

    /**
     * @testdox getTitle() returns title
     */
    public function testGetTitleReturnsTitle()
    {
        $wpPost   = WpMockFactory::createWpPost(['post_title' => 'Title']);
        $instance = new PostObjectFromWpPost(new PostObject(), $wpPost, new FakeWpService());

        $this->assertEquals('Title', $instance->getTitle());
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
