<?php

namespace Municipio\SchemaData\ExternalContent\SourceReaders\Logger;

interface LoggerInterface
{
    public function logError(string $message): void;
}