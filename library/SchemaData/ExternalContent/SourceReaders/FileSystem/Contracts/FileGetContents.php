<?php

namespace Municipio\SchemaData\ExternalContent\SourceReaders\FileSystem\Contracts;

interface FileGetContents
{
    /**
     * Reads entire file into a string.
     *
     * @param string $filename Name of the file to read.
     * @return string|false The function returns the read data or false on failure.
     */
    public function fileGetContents(string $filename): string|false;
}
