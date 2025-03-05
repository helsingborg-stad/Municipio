<?php

namespace Municipio\ExternalContent\SyncHandler\Cleanup;

use Municipio\ExternalContent\SyncHandler\SyncHandler;
use PHPUnit\Framework\TestCase;
use wpdb;
use WpService\Implementations\FakeWpService;

class CleanupAttachmentsNoLongerInUseTest extends TestCase
{
    /**
     * @testdox class can be instantiated
     */
    public function testClassCanBeInstantiated()
    {
        $cleanup = new CleanupAttachmentsNoLongerInUse(new FakeWpService(), new wpdb('', '', '', ''));
        $this->assertInstanceOf(CleanupAttachmentsNoLongerInUse::class, $cleanup);
    }

    /**
     * @testdox addHook adds a hook for the cleanup method
     */
    public function testAddHookAddsHookForCleanupMethod()
    {
        $wpService = new FakeWpService(['addAction' => true]);
        $cleanup   = new CleanupAttachmentsNoLongerInUse($wpService, new wpdb('', '', '', ''));

        $cleanup->addHooks();

        $this->assertEquals(SyncHandler::ACTION_AFTER, $wpService->methodCalls['addAction'][0][0]);
        $this->assertEquals([$cleanup, 'cleanup'], $wpService->methodCalls['addAction'][0][1]);
    }

    /**
     * @testdox Deletes attachments that are not longer in use.
     * @runInSeparateProcess
     */
    public function testDeletesAttachmentsNotInUse()
    {
        $attachmentId = 1;
        $wpService    = new FakeWpService();
        $wpdb         = $this->createMock(wpdb::class);
        $wpdb->method('get_results')->willReturn([(object)['post_id' => $attachmentId]]);
        $wpdb->method('prepare')->willReturnArgument(0);
        $wpdb->method('delete')->willReturn(true);

        $sut = new CleanupAttachmentsNoLongerInUse($wpService, $wpdb);
        $sut->cleanup();

        $this->assertArrayHasKey('wpDeleteAttachment', $wpService->methodCalls);
        $this->assertCount(1, $wpService->methodCalls['wpDeleteAttachment']);
        $this->assertEquals($attachmentId, $wpService->methodCalls['wpDeleteAttachment'][0][0]);
        $this->assertTrue($wpService->methodCalls['wpDeleteAttachment'][0][1]); // Force delete
    }
}
