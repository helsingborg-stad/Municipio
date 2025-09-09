<?php

namespace Municipio\ImageConvert\Logging;

interface LogWriterInterface
{
    public function write(string $formatted, LogEntry $entry): void;
}