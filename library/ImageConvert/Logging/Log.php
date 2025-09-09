<?php

namespace Municipio\ImageConvert\Logging;

use Municipio\ImageConvert\Contract\ImageContract;
use Municipio\ImageConvert\Logging\Writers\ErrorLogWriter;
use Municipio\ImageConvert\Logging\Writers\LogWriterInterface;

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
     * Specialized logging helper for logging messages related to a specific image.
     */
    public function logImage(
        object $context,
        string $message,
        ImageContract $image,
        LogLevel|string $level = LogLevel::INFO,
        array $metadata = []
    ): void {
        $metadata['image'] = $image;
        $this->log($context, $message, $level, $metadata);
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