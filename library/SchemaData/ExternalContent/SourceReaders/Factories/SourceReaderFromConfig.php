<?php

namespace Municipio\SchemaData\ExternalContent\SourceReaders\Factories;

use Municipio\SchemaData\ExternalContent\Config\SourceConfigInterface;
use Municipio\SchemaData\ExternalContent\Filter\SchemaObjectsFilter\SchemaObjectsFilterFromFilterDefinition;
use Municipio\SchemaData\ExternalContent\JsonToSchemaObjects\SimpleJsonConverter;
use Municipio\SchemaData\ExternalContent\Filter\Transforms\FilterDefinitionToTypesenseParams;
use Municipio\SchemaData\ExternalContent\SourceReaders\FileSystem\FileSystem;
use Municipio\SchemaData\ExternalContent\SourceReaders\HttpApi\TypesenseApi\TypesenseApi;
use Municipio\SchemaData\ExternalContent\SourceReaders\JsonFileSourceReader;
use Municipio\SchemaData\ExternalContent\SourceReaders\SourceReader;
use Municipio\SchemaData\ExternalContent\SourceReaders\SourceReaderInterface;
use Municipio\SchemaData\ExternalContent\SourceReaders\TypesenseSourceReader;
use Municipio\Helper\WpService;
use Municipio\SchemaData\ExternalContent\JsonToSchemaObjects\JsonConverterWithSanitizedProperties;
use Municipio\SchemaData\ExternalContent\JsonToSchemaObjects\SchemaSanitizer\SchemaSanitizer;
use Municipio\SchemaData\SchemaPropertyValueSanitizer\SchemaPropertyValueSanitizer;
use Municipio\SchemaData\Utils\GetSchemaPropertiesWithParamTypes;
use PHPUnit\Util\Json;

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
        return new JsonFileSourceReader($config->getSourceJsonFilePath(), $schemaObjectsFilter, new FileSystem(), new JsonConverterWithSanitizedProperties(
            new SchemaSanitizer(new SchemaPropertyValueSanitizer(), new GetSchemaPropertiesWithParamTypes()),
            new SimpleJsonConverter()
        ));
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

        return new TypesenseSourceReader($api, $getParamsString, new JsonConverterWithSanitizedProperties(
            new SchemaSanitizer(new SchemaPropertyValueSanitizer(), new GetSchemaPropertiesWithParamTypes()),
            new SimpleJsonConverter()
        ));
    }
}
