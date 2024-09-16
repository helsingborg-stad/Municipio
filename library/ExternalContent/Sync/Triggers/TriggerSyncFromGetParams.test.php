<?php

namespace Municipio\ExternalContent\Sync\Triggers;

use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class TriggerSyncFromGetParamsTest extends TestCase
{
    protected function tearDown(): void
    {
        $_GET = []; // Cleanup GET params
    }

    public function testShouldTrigger(): void
    {
        $wpService = new FakeWpService();
        $_GET      = [
            TriggerSyncFromGetParams::GET_PARAM_TRIGGER   => 'sync_external_content',
            TriggerSyncFromGetParams::GET_PARAM_POST_TYPE => 'post_type',
        ];

        (new TriggerSyncFromGetParams($wpService))->tryToTriggerSync();

        $this->assertEquals('Municipio/ExternalContent/Sync', $wpService->methodCalls['doAction'][0][0]);
    }

    public function testShouldNotTrigger(): void
    {
        $wpService = new FakeWpService();

        (new TriggerSyncFromGetParams($wpService))->tryToTriggerSync();

        $this->assertArrayNotHasKey('doAction', $wpService->methodCalls);
    }
}
