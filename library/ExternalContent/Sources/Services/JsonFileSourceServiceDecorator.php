<?php

namespace Municipio\ExternalContent\Sources\Services;

use Municipio\ExternalContent\Config\IJsonFileSourceConfig;
use Municipio\ExternalContent\JsonToSchemaObjects\JsonToSchemaObjects;
use Municipio\ExternalContent\Sources\ISource;
use Spatie\SchemaOrg\BaseType;
use WP_Query;
use WpService\FileSystem\GetFileContent;

class JsonFileSourceServiceDecorator implements ISource {

    public function __construct(
        private IJsonFileSourceConfig $config,
        private GetFileContent $fileSystem,
        private JsonToSchemaObjects $jsonToSchemaObjects,
        private ISource $inner = new NullSourceService()
        )
    {
    }

    public function getObject(string|int $id): BaseType
    {
        $fileContent = $this->fileSystem->getFileContent($this->config->getFile());
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
        return $this->jsonToSchemaObjects->transform( $this->fileSystem->getFileContent($this->config->getFile()) );
    }

    public function getPostType(): string
    {
        return $this->inner->getPostType();
    }

    public function getId(): string
    {
        return $this->inner->getId();
    }
}