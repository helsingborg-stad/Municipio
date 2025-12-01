<?php

namespace Municipio\ProgressReporter\OutputBuffer;

interface OutputBufferInterface
{
    /**
     * Flush the output buffer.
     *
     * @return void
     */
    public function flush(): void;

    /**
     * Disable the output buffer.
     *
     * @return void
     */
    public function disable(): void;
}
