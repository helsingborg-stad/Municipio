<?php

namespace Municipio\ProgressReporter;

use PHPUnit\Framework\TestCase;

class NullProgressReporterServiceTest extends TestCase
{
    #[TestDox('class can be instantiated')]
    public function testCanBeInstantiated()
    {
        $service = new NullProgressReporterService();
        $this->assertInstanceOf(NullProgressReporterService::class, $service);
    }

    #[TestDox('start() method does nothing')]
    public function testStartMethodDoesNothing()
    {
        $service = new NullProgressReporterService();
        $service->start();
        $this->expectOutputString('');
    }

    #[TestDox('setMessage() method does nothing')]
    public function testSetMessageMethodDoesNothing()
    {
        $service = new NullProgressReporterService();
        $service->setMessage('test');
        $this->expectOutputString('');
    }

    #[TestDox('setPercentage() method does nothing')]
    public function testSetPercentageMethodDoesNothing()
    {
        $service = new NullProgressReporterService();
        $service->setPercentage(50);
        $this->expectOutputString('');
    }

    #[TestDox('finish() method does nothing')]
    public function testFinishMethodDoesNothing()
    {
        $service = new NullProgressReporterService();
        $this->expectOutputString('', $service->finish('test'));
    }
}
