<?php

namespace Municipio\ExternalContent\SourceReaders;

use Municipio\ExternalContent\JsonToSchemaObjects\JsonToSchemaObjects;
use WpService\FileSystem\FileSystem;

class JsonFileSourceReader implements SourceReaderInterface
{
    public function __construct(
        private string $filePath,
        private FileSystem $fileSystem,
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

        $fileContent = $this->fileSystem->getFileContent($this->filePath);
        return $this->jsonToSchemaObjects->transform( $fileContent );
    }
}