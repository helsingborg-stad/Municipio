<?php

namespace Municipio\ExternalContent\Sync\Triggers;

use PHPUnit\Framework\TestCase;
use WpService\Contracts\DoAction;
use WpService\Implementations\FakeWpService;

class TriggerSyncTest extends TestCase
{
    /**
     * @testdox calls action with exact hook name, post type, post id if post id is set
     */
    public function testCallsActionWithPostIdIfPostIdIsSet()
    {
        // Given
        $inProgress = $this->getInProgress();
        $wpService  = new FakeWpService();
        $sut        = new TriggerSync($inProgress, $wpService);

        // When
        $sut->trigger('test_post_type', 123);

        // Then
        $this->assertEquals('Municipio/ExternalContent/Sync', $wpService->methodCalls['doAction'][0][0]);
        $this->assertEquals('test_post_type', $wpService->methodCalls['doAction'][0][1]);
        $this->assertEquals(123, $wpService->methodCalls['doAction'][0][2]);
    }

    public function testSyncIsNotPermittedIfInProgress()
    {
        $inProgress = $this->getInProgress();
        $inProgress->setInProgress(true);
        $wpService = new FakeWpService();
        $sut       = new TriggerSync($inProgress, $wpService);

        $sut->trigger('test_post_type', 123);

        $this->assertArrayNotHasKey('doAction', $wpService->methodCalls);
    }

    public function testInProgressIsSetToTrueWhenSyncStarts()
    {
        $inProgress = $this->getInProgress();
        $wpService  = new FakeWpService();
        $sut        = new TriggerSync($inProgress, $wpService);

        $sut->trigger('test_post_type', 123);

        $this->assertEquals(true, $inProgress->methodCalls['setInProgress'][0]);
    }

    public function testInProgressIsSetToFalseWhenSyncEnds()
    {
        $inProgress = $this->getInProgress();
        $wpService  = new FakeWpService();
        $sut        = new TriggerSync($inProgress, $wpService);

        $sut->trigger('test_post_type', 123);

        $this->assertEquals(false, $inProgress->methodCalls['setInProgress'][1]);
    }

    private function getInProgress(): InProgressInterface
    {
        return new class implements InProgressInterface {
            public array $methodCalls = [];
            private bool $inProgress  = false;

            public function isInProgress(): bool
            {
                return $this->inProgress;
            }

            public function setInProgress(bool $inProgress): void
            {
                $this->methodCalls[__FUNCTION__][] = $inProgress;
                $this->inProgress                  = $inProgress;
            }
        };
    }
}
