<?php

namespace Municipio\ImageConvert\Logging\Formatters;

use Municipio\ImageConvert\Logging\LogEntry;

interface LogFormatterInterface
{
    public function formatEntry(LogEntry $entry): string;
}
