<?php

namespace Municipio\ProgressReporter\OutputBuffer;

/**
 * Class OutputBuffer
 *
 * Flushes the output buffer.
 */
class OutputBuffer implements OutputBufferInterface
{
    /**
     * @inheritDoc
     */
    public function flush(): void
    {
        ob_flush();
        flush();
    }

    public function disable(): void
    {
        ob_end_flush();
    }
}
