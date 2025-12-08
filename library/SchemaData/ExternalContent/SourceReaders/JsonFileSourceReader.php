<?php

namespace Municipio\SchemaData\ExternalContent\SourceReaders;

use Municipio\SchemaData\ExternalContent\Exception\ExternalContentException;
use Municipio\SchemaData\ExternalContent\Filter\SchemaObjectsFilter\SchemaObjectsFilterInterface;
use Municipio\SchemaData\ExternalContent\JsonToSchemaObjects\JsonToSchemaObjectsInterface;
use Municipio\SchemaData\ExternalContent\SourceReaders\FileSystem\Contracts\FileExists;
use Municipio\SchemaData\ExternalContent\SourceReaders\FileSystem\Contracts\FileGetContents;

class JsonFileSourceReader implements SourceReaderInterface
{
    public function __construct(
        private string $filePath,
        private SchemaObjectsFilterInterface $schemaObjectsFilter,
        private FileGetContents&FileExists $fileSystem,
        private JsonToSchemaObjectsInterface $jsonToSchemaObjects,
    )
    {
    }

    /**
     * @inheritDoc
     */
    public function getSourceData(): array
    {
        if( ! $this->fileSystem->fileExists($this->filePath) ) {
            throw new ExternalContentException('Source file not found: ' . $this->filePath);
        }

        $fileContent = $this->fileSystem->fileGetContents($this->filePath);

        if(empty($fileContent)) {
            throw new ExternalContentException('Source file is empty: ' . $this->filePath);
        }

        $schemaObjects = $this->jsonToSchemaObjects->transform( $fileContent );
        
        return $this->schemaObjectsFilter->filter($schemaObjects);
    }
}