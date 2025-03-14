<?php

namespace Municipio\ProgressReporter\OutputBufferFlush;

/**
 * Class OutputBufferFlush
 *
 * Flushes the output buffer.
 */
class OutputBufferFlush implements OutputBufferFlushInterface
{
    /**
     * @inheritDoc
     */
    public function flush(): void
    {
        if (ob_get_length()) {
            ob_flush();
        }
        flush();
    }
}
