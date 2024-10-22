<?php

namespace Municipio\Comment;

use AcfService\AcfService;
use AcfService\Implementations\FakeAcfService;
use Municipio\TestUtils\WpMockFactory;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;
use WpService\WpService;

class OptionalHideDiscussionWhenLoggedOutTest extends TestCase
{
    private WpService $wpService;
    private AcfService $acfService;

    private function setUpServices(bool $userLoggedIn = false, mixed $optionValue = null): void
    {
        $this->wpService  = new FakeWpService(['isUserLoggedIn' => fn () => $userLoggedIn, 'addAction' => true]);
        $this->acfService = new FakeAcfService(['getField' => fn () => $optionValue]);
    }

    public function testAddHooks(): void
    {
        $this->setUpServices();
        $sut = new OptionalHideDiscussionWhenLoggedOut($this->wpService, $this->acfService);
        $sut->addHooks();

        $this->assertEquals('pre_get_comments', $this->wpService->methodCalls['addAction'][0][0]);
    }

    public function testNotHiddenWhenLoggedIn(): void
    {
        $this->setUpServices(true, true);
        $commentQuery = WpMockFactory::createWpCommentQuery(['query_vars' => []]);
        $sut          = new OptionalHideDiscussionWhenLoggedOut($this->wpService, $this->acfService);
        $sut->hideDiscussionFromLoggedOutUser($commentQuery);

        $this->assertArrayNotHasKey('post__in', $commentQuery->query_vars);
    }

    public function testNotHiddenWhenLoggedOutAndOptionDisabled(): void
    {
        $this->setUpServices(false, false);
        $commentQuery = WpMockFactory::createWpCommentQuery(['query_vars' => []]);
        $sut          = new OptionalHideDiscussionWhenLoggedOut($this->wpService, $this->acfService);
        $sut->hideDiscussionFromLoggedOutUser($commentQuery);

        $this->assertArrayNotHasKey('post__in', $commentQuery->query_vars);
    }

    public function testHiddenWhenLoggedOutAndOptionEnabled(): void
    {
        $this->setUpServices(false, true);
        $commentQuery = WpMockFactory::createWpCommentQuery(['query_vars' => []]);
        $sut          = new OptionalHideDiscussionWhenLoggedOut($this->wpService, $this->acfService);
        $sut->hideDiscussionFromLoggedOutUser($commentQuery);

        $this->assertEquals([0], $commentQuery->query_vars['post__in']);
    }
}
