<?php

namespace Municipio\ProgressReporter\HttpHeader;

use PHPUnit\Framework\TestCase;

class HttpHeaderTest extends TestCase
{
    protected function setUp(): void
    {
        $this->mockHeaderFunction();
    }

    /**
     * @testdox class can be instantiated
     */
    public function testCanBeInstantiated()
    {
        $header = new HttpHeader();
        $this->assertInstanceOf(HttpHeader::class, $header);
    }

    /**
     * @testdox sendHeader() method sends headers
     * @runInSeparateProcess
     */
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
