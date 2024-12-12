<?php

namespace Municipio\PostObject;

use Municipio\PostObject\PostObjectInterface;
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

        $instance = new PostObjectFromWpPost($wpPost, $wpService);

        $result = $instance->getCommentCount();

        $this->assertEquals(2, $result);
    }
}
