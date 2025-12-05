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
        if (ob_get_length() > 0) {
            ob_flush();
        }

        flush();
    }

    /**
     * Disables the output buffer by ending and flushing it.
     */
    public function disable(): void
    {
        ob_end_flush();
    }
}
