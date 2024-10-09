<?php

namespace Municipio\ExternalContent\Cron;

use Municipio\HooksRegistrar\Hookable;
use PHPUnit\Framework\TestCase;
use WpService\Contracts\AddAction;
use WpService\Contracts\AddFilter;
use WpService\Contracts\DoingCron;
use WpService\Implementations\FakeWpService;

class AllowCronToEditPostsTest extends TestCase
{
    /**
     * @testdox Does not add actions if cron is not a cron request
     */
    public function testDoesNotAddActionsIfCronIsNotRunning()
    {
        $wpService = new FakeWpService(['wpDoingCron' => false]);
        $sut       = new AllowCronToEditPosts($wpService);

        $sut->addHooks();

        $this->assertArrayNotHasKey('addAction', $wpService->methodCalls);
    }

    /**
     * @testdox Adds actions if cron is running
     */
    public function testAddsActionsIfCronIsRunning()
    {
        $wpService = new FakeWpService(['wpDoingCron' => true, 'addAction' => true]);
        $sut       = new AllowCronToEditPosts($wpService);

        $sut->addHooks();

        $this->assertEquals('Municipio/ExternalContent/Sync', $wpService->methodCalls['addAction'][0][0]);
        $this->assertEquals('Municipio/ExternalContent/Sync', $wpService->methodCalls['addAction'][1][0]);
    }

    /**
     * @testdox Adds capabilities with first action and removes on second
     */
    public function testAddsCapabilitiesWithFirstActionAndRemovesOnSecond()
    {
        $wpService = new FakeWpService(['wpDoingCron' => true, 'addAction' => true]);
        $sut       = new AllowCronToEditPosts($wpService);

        $sut->addHooks();

        $this->assertEquals('addCapabilitiesFilter', $wpService->methodCalls['addAction'][0][1][1]);
        $this->assertEquals('removeCapabilitiesFilter', $wpService->methodCalls['addAction'][1][1][1]);
    }

    /**
     * @testdox Second call to addAction() has higher priority than first
     */
    public function testSecondCallToAddActionHasHigherPriorityThanFirst()
    {
        $wpService = new FakeWpService(['wpDoingCron' => true, 'addAction' => true]);
        $sut       = new AllowCronToEditPosts($wpService);

        $sut->addHooks();

        $this->assertGreaterThan($wpService->methodCalls['addAction'][0][2], $wpService->methodCalls['addAction'][1][2]);
    }

    /**
     * @testdox allowEditPosts() returns $allcaps with edit_posts set to true if $args[0] is 'edit_posts'
     */
    public function testAllowEditPostsReturnsAllcapsWithEditPostsSetToTrueIfArgs0IsEditPosts()
    {
        $wpService = new FakeWpService(['doingCron' => true]);
        $sut       = new AllowCronToEditPosts($wpService);

        $result = $sut->allowEditPosts(['edit_posts' => false], [], ['edit_posts']);

        $this->assertTrue($result['edit_posts']);
    }
}
