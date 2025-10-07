<?php

namespace Municipio\ProgressReporter\HttpHeader;

use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class HttpHeaderTest extends TestCase
{
    protected function setUp(): void
    {
        $this->mockHeaderFunction();
    }

    #[TestDox('class can be instantiated')]
    public function testCanBeInstantiated()
    {
        $header = new HttpHeader();
        $this->assertInstanceOf(HttpHeader::class, $header);
    }

    #[TestDox('sendHeader() method sends headers')]
    #[RunInSeparateProcess]
    public function testSendHeaderMethodSendsHeaders()
    {
        ob_start();
        $header = new HttpHeader();
        $header
            ->sendHeader('Content-Type: text/event-stream')
            ->sendHeader('Cache-Control: no-cache')
            ->sendHeader('Connection: keep-alive');

        $output = ob_get_clean();

        $this->assertStringContainsString('Content-Type: text/event-stream', $output);
        $this->assertStringContainsString('Cache-Control: no-cache', $output);
        $this->assertStringContainsString('Connection: keep-alive', $output);
    }

    private function mockHeaderFunction()
    {
        function header(string $header, bool $replace = true, int $httpResponseCode = 0)
        {
            echo $header;
        }
    }
}
