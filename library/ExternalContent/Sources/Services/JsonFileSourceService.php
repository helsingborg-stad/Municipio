<?php

namespace Municipio\ExternalContent\Sources\Services;

use Municipio\ExternalContent\Config\IJsonFileSourceConfig;
use Municipio\ExternalContent\JsonToSchemaObjects\JsonToSchemaObjects;
use Municipio\ExternalContent\Sources\ISourceFilter;
use Municipio\ExternalContent\Sources\ISource;
use Spatie\SchemaOrg\Event;
use Spatie\SchemaOrg\Thing;
use WpService\FileSystem\GetFileContent;

class JsonFileSourceService implements ISource {

    public function __construct(
        private IJsonFileSourceConfig $config,
        private GetFileContent $fileSystem,
        private JsonToSchemaObjects $jsonToSchemaObjects
        )
    {
    }

    public function getObject(string|int $id): null|Thing|Event
    {
        $fileContent = $this->fileSystem->getFileContent($this->config->getFile());
        $objects = $this->jsonToSchemaObjects->transform( $fileContent );

        foreach($objects as $object) {
            if($object->getProperty('@id') == $id) {
                return $object;
            }
        }

        return null;
    }

    public function getObjects(?ISourceFilter $filter = null): array
    {
        return $this->jsonToSchemaObjects->transform( $this->fileSystem->getFileContent($this->config->getFile()) );
    }
}