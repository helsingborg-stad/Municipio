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
            return new TypesenseSourceService(
                new TypesenseClient($sourceConfig),
                $sourceConfig->getPostType(),
                new TryConvertTypesenseJsonToSchemaObjects()
            );
        } elseif ($sourceConfig instanceof IJsonFileSourceConfig) {
            return new JsonFileSourceService($sourceConfig, new BaseFileSystem(), new SimpleJsonConverter());
        }
    }
}
