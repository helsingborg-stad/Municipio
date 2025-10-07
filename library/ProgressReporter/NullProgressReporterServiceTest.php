<?php

namespace Municipio\ProgressReporter;

use PHPUnit\Framework\TestCase;

class NullProgressReporterServiceTest extends TestCase
{
    /**
     * @testdox class can be instantiated
     */
    public function testCanBeInstantiated()
    {
        $service = new NullProgressReporterService();
        $this->assertInstanceOf(NullProgressReporterService::class, $service);
    }

    /**
     * @testdox start() method does nothing
     */
    public function testStartMethodDoesNothing()
    {
        $service = new NullProgressReporterService();
        $service->start();
        $this->expectOutputString('');
    }

    /**
     * @testdox setMessage() method does nothing
     */
    public function testSetMessageMethodDoesNothing()
    {
        $service = new NullProgressReporterService();
        $service->setMessage('test');
        $this->expectOutputString('');
    }

    /**
     * @testdox setPercentage() method does nothing
     */
    public function testSetPercentageMethodDoesNothing()
    {
        $service = new NullProgressReporterService();
        $service->setPercentage(50);
        $this->expectOutputString('');
    }

    /**
     * @testdox finish() method does nothing
     */
    public function testFinishMethodDoesNothing()
    {
        $service = new NullProgressReporterService();
        $this->expectOutputString('', $service->finish('test'));
    }
}
