<?php

namespace Municipio\ProgressReporter\OutputBufferFlush;

use PHPUnit\Framework\TestCase;
use Municipio\ProgressReporter\OutputBufferFlush\OutputBufferFlush;

class OutputBufferFlushTest extends TestCase
{
    /**
     * Test that flush method calls ob_flush and flush when output buffer has content
     */
    public function testFlushWithContent()
    {
        $outputBufferFlush = new OutputBufferFlush();

        // Start output buffering and add content
        ob_start();
        echo 'test content';

        // Expect ob_flush and flush to be called
        $this->expectOutputString('test content');

        $outputBufferFlush->flush();

        // Clean up
        ob_end_clean();
    }

    /**
     * Test that flush method does not call ob_flush and flush when output buffer is empty
     */
    public function testFlushWithoutContent()
    {
        $outputBufferFlush = new OutputBufferFlush();

        // Start output buffering without adding content
        ob_start();

        // Expect no output
        $this->expectOutputString('');

        $outputBufferFlush->flush();

        // Clean up
        ob_end_clean();
    }
}
