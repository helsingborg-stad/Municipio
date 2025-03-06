<?php

namespace Municipio\ExternalContent\SourceReaders\FileSystem;

use Municipio\ExternalContent\SourceReaders\FileSystem\Contracts\FileExists;
use Municipio\ExternalContent\SourceReaders\FileSystem\Contracts\FileGetContents;

/**
 * Class Filesystem
 *
 * This class provides methods for interacting with the file system.
 */
class FileSystem implements FileExists, FileGetContents
{
    /**
     * @inheritDoc
     */
    public function fileExists(string $filename): bool
    {
        return file_exists($filename);
    }

    /**
     * @inheritDoc
     */
    public function fileGetContents(string $filename): string|false
    {
        return file_get_contents($filename);
    }
}
