<?php

namespace Municipio\Actions\Admin;

use PHPUnit\Framework\TestCase;
use WP_Screen;
use WpService\Implementations\FakeWpService;

class PostPageEditActionTest extends TestCase
{
    /**
     * @testdox class can be instantiated
     */
    public function testClassCanBeInstantiated()
    {
        $postPageEditAction = new PostPageEditAction(new FakeWpService());

        $this->assertInstanceOf(PostPageEditAction::class, $postPageEditAction);
    }

    /**
     * @testdox action runs after the `current_screen` hook
     */
    public function testActionRunsAfterCurrentScreenHook()
    {
        $wpService          = new FakeWpService(['addAction' => true]);
        $postPageEditAction = new PostPageEditAction($wpService);

        $postPageEditAction->addHooks();

        $this->assertEquals('current_screen', $wpService->methodCalls['addAction'][0][0]);
    }

    /**
     * @testdox action is not run if the screen is not a post
     */
    public function testActionIsNotRunIfScreenIsNotPost()
    {
        $wpService          = new FakeWpService();
        $postPageEditAction = new PostPageEditAction($wpService);
        $wpScreen           = new WP_Screen();
        $wpScreen->base     = 'not_post';

        $postPageEditAction->doAction($wpScreen);

        $this->assertArrayNotHasKey('doAction', $wpService->methodCalls);
    }

    /**
     * @testdox action is not run if the screen is post but no post type is set
     */
    public function testActionIsNotRunIfScreenIsPostButNoPostIdSet()
    {
        $wpService          = new FakeWpService();
        $postPageEditAction = new PostPageEditAction($wpService);
        $wpScreen           = new WP_Screen();
        $wpScreen->base     = 'post';

        $postPageEditAction->doAction($wpScreen);

        $this->assertArrayNotHasKey('doAction', $wpService->methodCalls);
    }

    /**
     * @testdox action is not run if no post id is present in the request
     */
    public function testActionIsNotRunIfNoPostIdPresentInRequest()
    {
        $wpService           = new FakeWpService();
        $postPageEditAction  = new PostPageEditAction($wpService);
        $wpScreen            = new WP_Screen();
        $wpScreen->base      = 'post';
        $wpScreen->post_type = 'page';

        $_GET['post'] = null;

        $postPageEditAction->doAction($wpScreen);

        $this->assertArrayNotHasKey('doAction', $wpService->methodCalls);
    }

    /**
     * @testdox action is run if the screen is post and post id is set
     */
    public function testActionIsRunIfScreenIsPostAndPostIdSet()
    {
        $wpService           = new FakeWpService();
        $postPageEditAction  = new PostPageEditAction($wpService);
        $wpScreen            = new WP_Screen();
        $wpScreen->base      = 'post';
        $wpScreen->post_type = 'page';

        $_GET['post'] = 123;

        $postPageEditAction->doAction($wpScreen);

        $this->assertCount(1, $wpService->methodCalls['doAction']);
        $this->assertEquals(PostPageEditAction::ACTION, $wpService->methodCalls['doAction'][0][0]);
        $this->assertEquals(123, $wpService->methodCalls['doAction'][0][1]);
        $this->assertEquals('page', $wpService->methodCalls['doAction'][0][2]);
    }
}
