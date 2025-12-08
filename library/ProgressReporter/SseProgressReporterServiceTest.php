<?php

namespace Municipio\ProgressReporter;

use Municipio\ProgressReporter\HttpHeader\HttpHeaderInterface;
use Municipio\ProgressReporter\OutputBuffer\OutputBufferInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class SseProgressReporterServiceTest extends TestCase
{
    #[TestDox('class can be instantiated')]
    public function testCanBeInstantiated()
    {
        $service = new SseProgressReporterService($this->getHttpHeader(), $this->getObFlushMock());
        $this->assertInstanceOf(SseProgressReporterService::class, $service);
    }

    #[TestDox('start() method sets headers')]
    public function testStartMethodSetsHeaders()
    {
        $service = new SseProgressReporterService($this->getHttpHeader(), $this->getObFlushMock());

        $this->expectOutputRegex('/Content-Type: text\/event-stream/');
        $this->expectOutputRegex('/X-Accel-Buffering: no/');
        $this->expectOutputRegex('/Cache-Control: no-cache/');

        $service->start();
    }

    #[TestDox('start() method sends start event')]
    public function testStartMethodSendsStartEvent()
    {
        $service = new SseProgressReporterService($this->getHttpHeader(), $this->getObFlushMock());

        $this->expectOutputRegex('/event: start\n/');

        $service->start();
    }

    #[TestDox('setMessage() method sends message event')]
    public function testSetMessageMethodSendsMessageEvent()
    {
        $service = new SseProgressReporterService($this->getHttpHeader(), $this->getObFlushMock());

        $this->expectOutputRegex('/event: message\n/');

        $service->setMessage('test');
    }

    private function getHttpHeader(): HttpHeaderInterface
    {
        return new class implements HttpHeaderInterface {
            public function sendHeader(string $header, bool $replace = true, int $httpResponseCode = 0): HttpHeaderInterface
            {
                echo $header;
                return $this;
            }
        };
    }

    private function getObFlushMock(): OutputBufferInterface|MockObject
    {
        return $this->createMock(OutputBufferInterface::class);
    }
}
