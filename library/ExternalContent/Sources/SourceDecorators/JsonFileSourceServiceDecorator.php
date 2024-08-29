<?php

namespace Municipio\ExternalContent\Sources\SourceDecorators;

use Municipio\Config\Features\ExternalContent\SourceConfig\JsonSourceConfigInterface;
use Municipio\ExternalContent\JsonToSchemaObjects\JsonToSchemaObjects;
use Municipio\ExternalContent\Sources\SourceInterface;
use Spatie\SchemaOrg\BaseType;
use WP_Query;
use WpService\FileSystem\GetFileContent;

class JsonFileSourceServiceDecorator implements SourceInterface {

    public function __construct(
        private JsonSourceConfigInterface $config,
        private GetFileContent $fileSystem,
        private JsonToSchemaObjects $jsonToSchemaObjects,
        private SourceInterface $inner
        )
    {
    }

    public function getObject(string|int $id): ?BaseType
    {
        $fileContent = $this->fileSystem->getFileContent($this->config->getFilePath());
        $objects = $this->jsonToSchemaObjects->transform( $fileContent );

        foreach($objects as $object) {
            if($object->getProperty('@id') == $id) {
                return $object;
            }
        }

        return $this->inner->getObject($id);
    }

    public function getObjects(?WP_Query $query = null): array
    {
        return $this->jsonToSchemaObjects->transform( $this->fileSystem->getFileContent($this->config->getFilePath()) );
    }

    public function getPostType(): string
    {
        return $this->inner->getPostType();
    }

    public function getId(): string
    {
        return $this->inner->getId();
    }

    public function getSchemaObjectType(): string
    {
        return $this->inner->getSchemaObjectType();
    }
}