<?php

declare(strict_types=1);


namespace Municipio\SchemaData\ExternalContent\SourceReaders\Logger;

class Logger implements LoggerInterface
{
    public function logError(string $message): void
    {
        error_log($message);
    }
}