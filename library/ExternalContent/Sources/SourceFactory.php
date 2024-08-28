<?php

namespace Municipio\ExternalContent\Sources;

use Municipio\ExternalContent\Config\JsonFileSourceConfigInterface;
use Municipio\ExternalContent\Config\SourceConfigInterface;
use Municipio\ExternalContent\Config\TypesenseSourceConfigInterface;
use Municipio\ExternalContent\JsonToSchemaObjects\SimpleJsonConverter;
use Municipio\ExternalContent\JsonToSchemaObjects\TryConvertTypesenseJsonToSchemaObjects;
use Municipio\ExternalContent\Sources\SourceDecorators\JsonFileSourceServiceDecorator;
use Municipio\ExternalContent\Sources\SourceDecorators\SourceServiceWithSourceId;
use Municipio\ExternalContent\Sources\SourceDecorators\FilterOutDuplicateObjectsFromSource;
use Municipio\ExternalContent\Sources\Services\TypesenseClient\TypesenseClient;
use Municipio\ExternalContent\Sources\SourceDecorators\TypesenseSourceServiceDecorator;
use WpService\FileSystem\BaseFileSystem;
use WpService\WpService;

class SourceFactory implements SourceFactoryInterface
{
    public function __construct(private WpService $wpService)
    {
    }

    /**
     * Create a source based on the given source configuration.
     *
     * @param SourceConfigInterface $sourceConfig The source configuration.
     * @return SourceInterface The created source.
     * @throws \Exception If the source configuration type is unknown.
     */
    public function createSource(SourceConfigInterface $sourceConfig): SourceInterface
    {
        $source = new Source(
            $sourceConfig->getPostType(),
            $sourceConfig->getSchemaObjectType()
        );

        if ($sourceConfig instanceof TypesenseSourceConfigInterface) {
            $souceServiceId = $sourceConfig->getPostType() . $sourceConfig->getHost() . $sourceConfig->getCollectionName();
            $souceServiceId = md5($souceServiceId);

            $source = new TypesenseSourceServiceDecorator(
                new TypesenseClient($sourceConfig),
                new TryConvertTypesenseJsonToSchemaObjects(),
                new SourceServiceWithSourceId($souceServiceId, $source)
            );
        } elseif ($sourceConfig instanceof JsonFileSourceConfigInterface) {
            $souceServiceId = $sourceConfig->getPostType() . $sourceConfig->getFile();
            $souceServiceId = md5($souceServiceId);

            $source = new JsonFileSourceServiceDecorator(
                $sourceConfig,
                new BaseFileSystem(),
                new SimpleJsonConverter(),
                new SourceServiceWithSourceId($souceServiceId, $source)
            );
        }

        $source = new FilterOutDuplicateObjectsFromSource($source); // TODO: Rename to FilterOutDuplicateObjectsFromSource

        return $source;
    }
}
