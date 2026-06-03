<?php

declare(strict_types=1);


namespace Municipio\SchemaData\ExternalContent\SourceReaders\Logger;

interface LoggerInterface
{
    public function logError(string $message): void;
}