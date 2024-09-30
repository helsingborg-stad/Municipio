<?php

namespace Municipio\ExternalContent\Sync\SyncInPorgress;

use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class PostTypeSyncInProgressTest extends TestCase
{
    public function testReturnsTrueIfSynchronizationIsInProgressForTheGivenPostType()
    {
        $wpService              = new FakeWpService(['getTransient' => true]);
        $postTypeSyncInProgress = new PostTypeSyncInProgress($wpService);

        $this->assertTrue($postTypeSyncInProgress->isInProgress('post'));
    }

    public function testReturnsFalseIfSynchronizationIsNotInProgressForTheGivenPostType()
    {
        $wpService              = new FakeWpService(['getTransient' => false]);
        $postTypeSyncInProgress = new PostTypeSyncInProgress($wpService);

        $this->assertFalse($postTypeSyncInProgress->isInProgress('post'));
    }

    public function testSetsTheSynchronizationStatusForTheGivenPostType()
    {
        $wpService              = new FakeWpService();
        $postTypeSyncInProgress = new PostTypeSyncInProgress($wpService);
        $postTypeSyncInProgress->setInProgress('post', true);

        $this->assertEquals(
            $postTypeSyncInProgress->getTransientName('post'),
            $wpService->methodCalls['setTransient'][0][0]
        );
        $this->assertTrue($wpService->methodCalls['setTransient'][0][1]);
    }

    public function testDeletesTheSynchronizationStatusForTheGivenPostType()
    {
        $wpService              = new FakeWpService();
        $postTypeSyncInProgress = new PostTypeSyncInProgress($wpService);
        $postTypeSyncInProgress->setInProgress('post', false);

        $this->assertEquals(
            $postTypeSyncInProgress->getTransientName('post'),
            $wpService->methodCalls['deleteTransient'][0][0]
        );
    }
}
