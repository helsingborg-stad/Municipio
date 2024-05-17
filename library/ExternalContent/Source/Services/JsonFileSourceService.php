<?php

namespace Municipio\ExternalContent\Source\Services;

use Municipio\ExternalContent\JsonToSchemaObjects\JsonToSchemaObjects;
use Municipio\ExternalContent\Source\SchemaSourceFilter;
use Municipio\ExternalContent\Source\SchemaSourceReader;
use Spatie\SchemaOrg\Thing;
use WpService\FileSystem\GetFileContent;

class JsonFileSourceService implements SchemaSourceReader {

    public function __construct(private string $fileLocation, private GetFileContent $fileSystem, private JsonToSchemaObjects $jsonToSchemaObjects)
    {
    }

    public function getObject(string|int $id): ?object
    {
        $fileContent = $this->fileSystem->getFileContent($this->fileLocation);
        $objects = $this->jsonToSchemaObjects->transform( $fileContent );

        foreach($objects as $object) {
            if($object->getProperty('@id') == $id) {
                return $object;
            }
        }

        return null;
    }

    public function getObjects(?SchemaSourceFilter $filter = null): array
    {
        return $this->jsonToSchemaObjects->transform( $this->fileSystem->getFileContent($this->fileLocation) );
    }
}