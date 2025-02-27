<?php

namespace Municipio\ExternalContent\SourceReaders\FileSystem\Contracts;

interface FileExists
{
    /**
     * Checks whether a file or directory exists
     *
     * @param string $filename Path to the file or directory.
     * @return bool Returns true if the file or directory specified by filename exists; false otherwise.
     */
    public function fileExists(string $filename): bool;
}
