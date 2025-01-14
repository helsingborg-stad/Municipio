<?php

namespace Municipio\PostObject\Decorators;

use Municipio\PostObject\Icon\IconInterface;
use Municipio\PostObject\PostObject;
use Municipio\PostObject\PostObjectInterface;
use PHPUnit\Framework\TestCase;
use WpService\Contracts\GetCurrentBlogId;
use WpService\Contracts\MsIsSwitched;
use WpService\Implementations\FakeWpService;

class PostObjectWithOtherBlogIdFromSwitchedStateTest extends TestCase
{
    /**
     * @testdox class can be instantiated
     */
    public function testCanBeInstantiated()
    {
        $wpService  = new FakeWpService(['isMultisite' => true, 'msIsSwitched' => false, 'getCurrentBlogId' => 1]);
        $postObject = new PostObject($wpService);

        $this->assertInstanceOf(
            PostObjectWithOtherBlogIdFromSwitchedState::class,
            new PostObjectWithOtherBlogIdFromSwitchedState($postObject, $wpService)
        );
    }

    /**
     * @testdox returns blog id from inner post object if not switched
     */
    public function testReturnsBlogIdFromInnerPostObjectIfNotSwitched()
    {
        $wpService  = new FakeWpService(['isMultisite' => true, 'msIsSwitched' => false, 'getCurrentBlogId' => 1]);
        $postObject = new PostObject($wpService);

        $result = new PostObjectWithOtherBlogIdFromSwitchedState($postObject, $wpService);

        $this->assertEquals(1, $result->getBlogId());
    }

    /**
     * @testdox returns blog id from wp service if switched
     */
    public function testReturnsBlogIdFromWpServiceIfSwitched()
    {
        $wpService  = new FakeWpService(['isMultisite' => true, 'msIsSwitched' => true, 'getCurrentBlogId' => 2]);
        $postObject = new PostObject($wpService);

        $result = new PostObjectWithOtherBlogIdFromSwitchedState($postObject, $wpService);

        $this->assertEquals(2, $result->getBlogId());
    }
}
