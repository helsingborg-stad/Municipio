<?php

namespace Municipio\ImageConvert\Logging;

use Municipio\ImageConvert\Logging\Writers\ErrorLogWriter;
use Municipio\ImageConvert\Logging\Writers\LogWriterInterface;
use Municipio\ImageConvert\Logging\Formatters\DefaultFormatter;
use Municipio\ImageConvert\Logging\Formatters\LogFormatterInterface;

class Log
{
    protected LogFormatterInterface $formatter;
    protected LogWriterInterface $writer;

    public function __construct(?LogFormatterInterface $formatter, ?LogWriterInterface $writer){
        $this->formatter = $formatter ?? new DefaultFormatter();
        $this->writer = $writer ?? new ErrorLogWriter();
    }

    /**
     * Oneline logging helper for quick logging without needing to create a LogEntry instance.
     */
    public function log(
        object $context,
        string $message,
        LogLevel|string $level = LogLevel::INFO,
        array $metadata = []
    ): void {
        $entry = (new LogEntry($this))
            ->context($context)
            ->setLevel($level)
            ->setMetadata($metadata)
            ->message($message);

        $this->writeEntry($entry);
    }

    /**
     * Start a new log entry with a context object.
     */
    public function add(object $context): LogEntry
    {
        return (new LogEntry($this))->context($context);
    }

    /**
     * Internal method to write a log entry.
     */
    public function writeEntry(LogEntry $entry): void
    {
        $formatted = $this->formatter->formatEntry($entry);
        $this->writer->write($formatted, $entry);
    }
}