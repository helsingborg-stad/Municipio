<?php

namespace Municipio\ExternalContent\SyncHandler;

use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class SyncHandlerTest extends TestCase
{
    /**
     * @testdox class can be instantiated
     */
    public function testClassCanBeInstantiated()
    {
        $syncHandler = new SyncHandler([], new FakeWpService());

        $this->assertInstanceOf(SyncHandler::class, $syncHandler);
    }
}
