<?php

namespace Municipio\ImageConvert\Logging;

class ErrorLogWriter implements LogWriterInterface
{
    public function write(string $formatted, LogEntry $entry): void
    {
        error_log($formatted);
    }
}