<?php

namespace Municipio\ImageConvert\Logging\Writers;

use Municipio\ImageConvert\Logging\LogEntry;

interface LogWriterInterface
{
    public function write(string $formatted, LogEntry $entry): void;
}
