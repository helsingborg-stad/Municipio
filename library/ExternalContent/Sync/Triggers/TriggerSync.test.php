<?php

namespace Municipio\ExternalContent\Sync\Triggers;

use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class TriggerSyncTest extends TestCase
{
    /**
     * @testdox Calls action with exact hook name, post type, post id if post id is set and sync is not in progress
     */
    public function testCallsActionWithPostIdIfPostIdIsSetAndSyncIsNotInProgress()
    {
        $inProgress = $this->getInProgress();
        $wpService  = new FakeWpService();
        $sut        = new TriggerSync($inProgress, $wpService);

        $sut->trigger('test_post_type', 123);

        $this->assertEquals('Municipio/ExternalContent/Sync', $wpService->methodCalls['doAction'][0][0]);
        $this->assertEquals('test_post_type', $wpService->methodCalls['doAction'][0][1]);
        $this->assertEquals(123, $wpService->methodCalls['doAction'][0][2]);
    }

    /**
     * @testdox Does not trigger sync if sync is in progress
     */
    public function testDoesNotTriggerSyncIfSyncIsInProgress()
    {
        $inProgress = $this->getInProgress();
        $inProgress->setInProgress(true);
        $wpService = new FakeWpService();
        $sut       = new TriggerSync($inProgress, $wpService);

        $sut->trigger('test_post_type', 123);

        $this->assertArrayNotHasKey('doAction', $wpService->methodCalls);
    }

    /**
     * @testdox Sets in progress to true when triggering sync
     */
    public function testSetsInProgressToTrueWhenTriggeringSync()
    {
        $inProgress = $this->getInProgress();
        $wpService  = new FakeWpService();
        $sut        = new TriggerSync($inProgress, $wpService);

        $sut->trigger('test_post_type', 123);

        $this->assertTrue($inProgress->calls[0]);
    }

    /**
     * @testdox Sets in progress to false after sync
     */
    public function testSetsInProgressToFalseAfterSync()
    {
        $inProgress = $this->getInProgress();
        $wpService  = new FakeWpService();
        $sut        = new TriggerSync($inProgress, $wpService);

        $sut->trigger('test_post_type', 123);

        $this->assertFalse($inProgress->calls[1]);
    }

    private function getInProgress(): InProgressInterface
    {
        return new class implements InProgressInterface {
            private bool $inProgress = false;
            public array $calls      = [];

            public function isInProgress(): bool
            {
                return $this->inProgress;
            }

            public function setInProgress(bool $inProgress): void
            {
                $this->calls[]    = $inProgress;
                $this->inProgress = $inProgress;
            }
        };
    }
}
