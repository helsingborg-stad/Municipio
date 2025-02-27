<?php

namespace Municipio\ExternalContent\SourceReaders;

use Municipio\ExternalContent\JsonToSchemaObjects\JsonToSchemaObjects;
use Municipio\ExternalContent\SourceReaders\FileSystem\Contracts\FileExists;
use Municipio\ExternalContent\SourceReaders\FileSystem\Contracts\FileGetContents;

class JsonFileSourceReader implements SourceReaderInterface
{
    public function __construct(
        private string $filePath,
        private FileGetContents&FileExists $fileSystem,
        private JsonToSchemaObjects $jsonToSchemaObjects,
    )
    {
    }

    /**
     * @inheritDoc
     */
    public function getSourceData(): array
    {
        if( ! $this->fileSystem->fileExists($this->filePath) ) {
            throw new \InvalidArgumentException('File does not exist');
        }

        $fileContent = $this->fileSystem->fileGetContents($this->filePath);
        return $this->jsonToSchemaObjects->transform( $fileContent );
    }
}