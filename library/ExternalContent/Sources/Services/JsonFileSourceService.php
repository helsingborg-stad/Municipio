<?php

namespace Municipio\ExternalContent\Sources\Services;

use Municipio\ExternalContent\Config\IJsonFileSourceConfig;
use Municipio\ExternalContent\JsonToSchemaObjects\JsonToSchemaObjects;
use Municipio\ExternalContent\Sources\ISource;
use Spatie\SchemaOrg\Event;
use Spatie\SchemaOrg\Thing;
use WP_Query;
use WpService\FileSystem\GetFileContent;

class JsonFileSourceService implements ISource {

    public function __construct(
        private IJsonFileSourceConfig $config,
        private GetFileContent $fileSystem,
        private JsonToSchemaObjects $jsonToSchemaObjects,
        private ?ISource $inner = null
        )
    {
        if( $this->inner === null ) {
            $this->inner = new SourceService($this->config->getPostType());
        }
    }

    public function getId(): int
    {
        return $this->inner->getId();
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

    public function getObjects(?WP_Query $query = null): array
    {
        return $this->jsonToSchemaObjects->transform( $this->fileSystem->getFileContent($this->config->getFile()) );
    }

    public function getPostType(): string
    {
        return $this->inner->getPostType();
    }
}