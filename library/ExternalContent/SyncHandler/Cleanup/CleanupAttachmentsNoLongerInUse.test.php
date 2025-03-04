<?php

namespace Municipio\ExternalContent\SyncHandler\Cleanup;

use Municipio\ExternalContent\SyncHandler\SyncHandler;
use Municipio\TestUtils\WpMockFactory;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class CleanupAttachmentsNoLongerInUseTest extends TestCase
{
    /**
     * @testdox class can be instantiated
     */
    public function testClassCanBeInstantiated()
    {
        $cleanup = new CleanupAttachmentsNoLongerInUse(new FakeWpService(), WpMockFactory::createWpdb());
        $this->assertInstanceOf(CleanupAttachmentsNoLongerInUse::class, $cleanup);
    }

    /**
     * @testdox addHook adds a hook for the cleanup method
     */
    public function testAddHookAddsHookForCleanupMethod()
    {
        $wpService = new FakeWpService(['addAction' => true]);
        $cleanup   = new CleanupAttachmentsNoLongerInUse($wpService, WpMockFactory::createWpdb());

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
        $wpdb         = WpMockFactory::createWpdb([
            'postmeta'    => 'postmeta',
            'prepare'     => fn($query, ...$args) => $query,
            'get_results' => fn() => [(object)['post_id' => $attachmentId]]]);

        $sut = new CleanupAttachmentsNoLongerInUse($wpService, $wpdb);
        $sut->cleanup();

        $this->assertArrayHasKey('wpDeleteAttachment', $wpService->methodCalls);
        $this->assertCount(1, $wpService->methodCalls['wpDeleteAttachment']);
        $this->assertEquals($attachmentId, $wpService->methodCalls['wpDeleteAttachment'][0][0]);
        $this->assertTrue($wpService->methodCalls['wpDeleteAttachment'][0][1]); // Force delete
    }
}
