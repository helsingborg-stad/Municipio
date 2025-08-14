<?php

namespace Municipio\ExternalContent\SyncHandler;

use Municipio\ProgressReporter\ProgressReporterInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class SyncHandlerTest extends TestCase
{
    /**
     * @testdox class can be instantiated
     */
    public function testClassCanBeInstantiated()
    {
        $syncHandler = new SyncHandler([], new FakeWpService(), $this->getProgressReporter());

        $this->assertInstanceOf(SyncHandler::class, $syncHandler);
    }

    private function getProgressReporter(): ProgressReporterInterface|MockObject
    {
        return $this->createMock(ProgressReporterInterface::class);
    }
}
