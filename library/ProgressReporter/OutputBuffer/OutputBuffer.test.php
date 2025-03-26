<?php

namespace Municipio\ProgressReporter\OutputBuffer;

use PHPUnit\Framework\TestCase;
use Municipio\ProgressReporter\OutputBuffer\OutputBuffer;

class OutputBufferTest extends TestCase
{
    /**
     * Test that flush method calls ob_flush and flush when output buffer has content
     */
    public function testFlushWithContent()
    {
        $outputBuffer = new OutputBuffer();

        // Start output buffering and add content
        ob_start();
        echo 'test content';

        // Expect ob_flush and flush to be called
        $this->expectOutputString('test content');

        $outputBuffer->flush();

        // Clean up
        ob_end_clean();
    }

    /**
     * Test that flush method does not call ob_flush and flush when output buffer is empty
     */
    public function testFlushWithoutContent()
    {
        $outputBuffer = new OutputBuffer();

        // Start output buffering without adding content
        ob_start();

        // Expect no output
        $this->expectOutputString('');

        $outputBuffer->flush();

        // Clean up
        ob_end_clean();
    }

    /**
     * Test that disable method calls ob_end_flush
     */
    public function testDisable()
    {
        $outputBuffer = new OutputBuffer();

        $outputBuffer->disable();

        $this->assertEquals(0, ob_get_level());
        $this->assertEmpty(ob_get_status());

        // enable output buffering again to avoid errors in other tests
        ob_start();
    }
}
