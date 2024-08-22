<?php

namespace Municipio\ExternalContent\Sources;

use Municipio\ExternalContent\Config\IJsonFileSourceConfig;
use Municipio\ExternalContent\Config\ISourceConfig;
use Municipio\ExternalContent\Config\ITypesenseSourceConfig;
use Municipio\ExternalContent\JsonToSchemaObjects\SimpleJsonConverter;
use Municipio\ExternalContent\JsonToSchemaObjects\TryConvertTypesenseJsonToSchemaObjects;
use Municipio\ExternalContent\Sources\Services\JsonFileSourceServiceDecorator;
use Municipio\ExternalContent\Sources\Services\Source;
use Municipio\ExternalContent\Sources\Services\SourceServiceWithPostType;
use Municipio\ExternalContent\Sources\Services\SourceServiceWithSourceId;
use Municipio\ExternalContent\Sources\Services\TypesenseClient\TypesenseClient;
use Municipio\ExternalContent\Sources\Services\TypesenseSourceServiceDecorator;
use WpService\FileSystem\BaseFileSystem;

class SourceFactory implements ISourceFactory
{
    /**
     * Create a source based on the given source configuration.
     *
     * @param ISourceConfig $sourceConfig The source configuration.
     * @return ISource The created source.
     * @throws \Exception If the source configuration type is unknown.
     */
    public function createSource(ISourceConfig $sourceConfig): ISource
    {
        if ($sourceConfig instanceof ITypesenseSourceConfig) {
            $souceServiceId = $sourceConfig->getPostType() . $sourceConfig->getHost() . $sourceConfig->getCollectionName();
            $souceServiceId = md5($souceServiceId);

            return new TypesenseSourceServiceDecorator(
                new TypesenseClient($sourceConfig),
                new TryConvertTypesenseJsonToSchemaObjects(),
                new SourceServiceWithSourceId($souceServiceId, new Source(
                    $sourceConfig->getPostType(),
                    $sourceConfig->getSchemaObjectType()
                ))
            );
        } elseif ($sourceConfig instanceof IJsonFileSourceConfig) {
            $souceServiceId = $sourceConfig->getPostType() . $sourceConfig->getFile();
            $souceServiceId = md5($souceServiceId);

            return new JsonFileSourceServiceDecorator(
                $sourceConfig,
                new BaseFileSystem(),
                new SimpleJsonConverter(),
                new SourceServiceWithSourceId($souceServiceId, new Source(
                    $sourceConfig->getPostType(),
                    $sourceConfig->getSchemaObjectType()
                ))
            );
        }

        throw new \Exception('Unknown source config type');
    }
}
