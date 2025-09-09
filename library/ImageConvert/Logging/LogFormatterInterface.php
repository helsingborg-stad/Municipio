<?php

namespace Municipio\ImageConvert\Logging;

interface LogFormatterInterface
{
    public function formatEntry(LogEntry $entry): string;
}