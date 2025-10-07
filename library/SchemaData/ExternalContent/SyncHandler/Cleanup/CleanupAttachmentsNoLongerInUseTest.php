<?php

namespace Municipio\SchemaData\ExternalContent\SyncHandler\Cleanup;

use Municipio\SchemaData\ExternalContent\SyncHandler\SyncHandler;
use PHPUnit\Framework\TestCase;
use wpdb;
use WpService\Implementations\FakeWpService;

class CleanupAttachmentsNoLongerInUseTest extends TestCase
{
    #[TestDox('class can be instantiated')]
    public function testClassCanBeInstantiated()
    {
        $cleanup = new CleanupAttachmentsNoLongerInUse(new FakeWpService(), new wpdb('', '', '', ''));
        $this->assertInstanceOf(CleanupAttachmentsNoLongerInUse::class, $cleanup);
    }

    #[TestDox('addHook adds a hook for the cleanup method')]
    public function testAddHookAddsHookForCleanupMethod()
    {
        $wpService = new FakeWpService(['addAction' => true]);
        $cleanup   = new CleanupAttachmentsNoLongerInUse($wpService, new wpdb('', '', '', ''));

        $cleanup->addHooks();

        $this->assertEquals(SyncHandler::ACTION_AFTER, $wpService->methodCalls['addAction'][0][0]);
        $this->assertEquals([$cleanup, 'cleanup'], $wpService->methodCalls['addAction'][0][1]);
    }
}
