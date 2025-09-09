<?php

namespace Municipio\ImageConvert\Logging\Writers;

use Municipio\ImageConvert\Logging\LogEntry;
use Municipio\ImageConvert\Logging\Writers\LogWriterInterface;

class ErrorLogWriter implements LogWriterInterface
{
    public function write(string $formatted, LogEntry $entry): void
    {
        error_log($formatted);
    }
}