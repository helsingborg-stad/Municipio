<?php

namespace Municipio\ProgressReporter\OutputBufferFlush;

interface OutputBufferFlushInterface
{
    /**
     * Flush the output buffer.
     *
     * @return void
     */
    public function flush(): void;
}
