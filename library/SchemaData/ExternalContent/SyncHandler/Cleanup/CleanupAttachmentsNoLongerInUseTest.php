<?php

namespace Municipio\SchemaData\ExternalContent\SyncHandler\Cleanup;

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
}
