<?php

namespace Municipio\ExternalContent\Sources;

use Municipio\ExternalContent\Config\IJsonFileSourceConfig;
use Municipio\ExternalContent\Config\ISourceConfig;
use Municipio\ExternalContent\Config\ITypesenseSourceConfig;
use Municipio\ExternalContent\JsonToSchemaObjects\SimpleJsonConverter;
use Municipio\ExternalContent\JsonToSchemaObjects\TryConvertTypesenseJsonToSchemaObjects;
use Municipio\ExternalContent\Sources\Services\DecorateSchemaObjectsWithLocalIds;
use Municipio\ExternalContent\Sources\Services\JsonFileSourceService;
use Municipio\ExternalContent\Sources\Services\TypesenseClient\TypesenseClient;
use Municipio\ExternalContent\Sources\Services\TypesenseSourceService;
use WpService\FileSystem\BaseFileSystem;

class SourceFactory implements ISourceFactory
{
    public function createSource(int $id, ISourceConfig $sourceConfig): ISource
    {
        if ($sourceConfig instanceof ITypesenseSourceConfig) {
            $source = new TypesenseSourceService(
                new TypesenseClient($sourceConfig),
                $sourceConfig->getPostType(),
                $sourceConfig->getSchemaObjectType(),
                new TryConvertTypesenseJsonToSchemaObjects()
            );
        } elseif ($sourceConfig instanceof IJsonFileSourceConfig) {
            $source = new JsonFileSourceService($sourceConfig, new BaseFileSystem(), new SimpleJsonConverter());
        }

        return new DecorateSchemaObjectsWithLocalIds($source);
    }
}
