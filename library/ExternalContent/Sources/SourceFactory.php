<?php

namespace Municipio\ExternalContent\Sources;

use Municipio\ExternalContent\Config\JsonFileSourceConfigInterface;
use Municipio\ExternalContent\Config\SourceConfigInterface;
use Municipio\ExternalContent\Config\TypesenseSourceConfigInterface;
use Municipio\ExternalContent\JsonToSchemaObjects\SimpleJsonConverter;
use Municipio\ExternalContent\JsonToSchemaObjects\TryConvertTypesenseJsonToSchemaObjects;
use Municipio\ExternalContent\Sources\Services\JsonFileSourceServiceDecorator;
use Municipio\ExternalContent\Sources\Services\Source;
use Municipio\ExternalContent\Sources\Services\SourceServiceWithPostType;
use Municipio\ExternalContent\Sources\Services\SourceServiceWithSourceId;
use Municipio\ExternalContent\Sources\Services\SourceWithUniqueObjects;
use Municipio\ExternalContent\Sources\Services\TypesenseClient\TypesenseClient;
use Municipio\ExternalContent\Sources\Services\TypesenseSourceServiceDecorator;
use WpService\FileSystem\BaseFileSystem;

class SourceFactory implements SourceFactoryInterface
{
    /**
     * Create a source based on the given source configuration.
     *
     * @param SourceConfigInterface $sourceConfig The source configuration.
     * @return SourceInterface The created source.
     * @throws \Exception If the source configuration type is unknown.
     */
    public function createSource(SourceConfigInterface $sourceConfig): SourceInterface
    {
        $source = null;

        if ($sourceConfig instanceof TypesenseSourceConfigInterface) {
            $souceServiceId = $sourceConfig->getPostType() . $sourceConfig->getHost() . $sourceConfig->getCollectionName();
            $souceServiceId = md5($souceServiceId);

            $source = new TypesenseSourceServiceDecorator(
                new TypesenseClient($sourceConfig),
                new TryConvertTypesenseJsonToSchemaObjects(),
                new SourceServiceWithSourceId($souceServiceId, new Source(
                    $sourceConfig->getPostType(),
                    $sourceConfig->getSchemaObjectType()
                ))
            );
        } elseif ($sourceConfig instanceof JsonFileSourceConfigInterface) {
            $souceServiceId = $sourceConfig->getPostType() . $sourceConfig->getFile();
            $souceServiceId = md5($souceServiceId);

            $source = new JsonFileSourceServiceDecorator(
                $sourceConfig,
                new BaseFileSystem(),
                new SimpleJsonConverter(),
                new SourceServiceWithSourceId($souceServiceId, new Source(
                    $sourceConfig->getPostType(),
                    $sourceConfig->getSchemaObjectType()
                ))
            );
        }

        if ($source !== null) {
            return new SourceWithUniqueObjects($source);
        }

        throw new \Exception('Unknown source config type');
    }
}
