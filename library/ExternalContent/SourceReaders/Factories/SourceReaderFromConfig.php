<?php

namespace Municipio\ExternalContent\SourceReaders\Factories;

use Municipio\ExternalContent\Config\SourceConfigInterface;
use Municipio\ExternalContent\Filter\SchemaObjectsFilter\SchemaObjectsFilterFromFilterDefinition;
use Municipio\ExternalContent\JsonToSchemaObjects\SimpleJsonConverter;
use Municipio\ExternalContent\Filter\Transforms\FilterDefinitionToTypesenseParams;
use Municipio\ExternalContent\SourceReaders\FileSystem\FileSystem;
use Municipio\ExternalContent\SourceReaders\HttpApi\TypesenseApi\TypesenseApi;
use Municipio\ExternalContent\SourceReaders\JsonFileSourceReader;
use Municipio\ExternalContent\SourceReaders\SourceReader;
use Municipio\ExternalContent\SourceReaders\SourceReaderInterface;
use Municipio\ExternalContent\SourceReaders\TypesenseSourceReader;
use Municipio\Helper\WpService;

/**
 * Class SourceReaderFromConfig
 *
 * Create a SourceReader based on a SourceConfig
 */
class SourceReaderFromConfig implements SourceReaderFromConfigInterface
{
    /**
     * Constructor
     */
    public function __construct()
    {
    }

    /**
     * @inheritDoc
     */
    public function create(SourceConfigInterface $config): SourceReaderInterface
    {
        return match ($config->getSourceType()) {
            'json' => $this->getJsonFileSourceReader($config),
            'typesense' => $this->getTypesenseSourceReader($config),
            default => new SourceReader()
        };
    }

    /**
     * Creates and returns a JsonFileSourceReader instance based on the provided configuration.
     *
     * @param SourceConfigInterface $config The configuration object for the source reader.
     * @return JsonFileSourceReader The created JsonFileSourceReader instance.
     */
    private function getJsonFileSourceReader(SourceConfigInterface $config): JsonFileSourceReader
    {
        $schemaObjectsFilter = new SchemaObjectsFilterFromFilterDefinition($config->getFilterDefinition());
        return new JsonFileSourceReader($config->getSourceJsonFilePath(), $schemaObjectsFilter, new FileSystem(), new SimpleJsonConverter());
    }

    /**
     * Creates and returns a TypesenseSourceReader instance based on the provided configuration.
     *
     * @param SourceConfigInterface $config The configuration object for the source reader.
     * @return TypesenseSourceReader The created TypesenseSourceReader instance.
     */
    private function getTypesenseSourceReader(SourceConfigInterface $config): TypesenseSourceReader
    {
        $api                               = new TypesenseApi($config, WpService::get());
        $filterDefinitionToTypesenseParams = new FilterDefinitionToTypesenseParams();
        $getParamsString                   = $filterDefinitionToTypesenseParams->transform($config->getFilterDefinition());
        $getParamsString                   = !empty($getParamsString) ? '?' . $getParamsString : ''; // Add '?' if there are any parameters

        return new TypesenseSourceReader($api, $getParamsString, new SimpleJsonConverter());
    }
}
