<?php

namespace Municipio\SchemaData\ExternalContent\SourceReaders;

use Municipio\SchemaData\ExternalContent\Filter\SchemaObjectsFilter\SchemaObjectsFilterInterface;
use Municipio\SchemaData\ExternalContent\JsonToSchemaObjects\JsonToSchemaObjects;
use Municipio\SchemaData\ExternalContent\SourceReaders\FileSystem\Contracts\FileExists;
use Municipio\SchemaData\ExternalContent\SourceReaders\FileSystem\Contracts\FileGetContents;

class JsonFileSourceReader implements SourceReaderInterface
{
    public function __construct(
        private string $filePath,
        private SchemaObjectsFilterInterface $schemaObjectsFilter,
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
        $schemaObjects = $this->jsonToSchemaObjects->transform( $fileContent );
        
        return $this->schemaObjectsFilter->filter($schemaObjects);
    }
}