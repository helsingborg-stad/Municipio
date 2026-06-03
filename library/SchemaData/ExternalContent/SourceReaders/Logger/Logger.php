<?php

namespace Municipio\SchemaData\ExternalContent\SourceReaders\Logger;

class Logger implements LoggerInterface
{
    public function logError(string $message): void
    {
        error_log($message);
    }
}