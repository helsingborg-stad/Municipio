<?php

namespace Municipio\SchemaData;

use Municipio\HooksRegistrar\Hookable;
use WpService\Contracts\AddFilter;

/**
 * Class LimitSchemaTypesAndProperties
 *
 * This class limits the schema types and properties based on the provided configuration.
 */
class LimitSchemaTypesAndProperties implements Hookable
{
    /**
     * Constructor.
     *
     * @param array $allowedSchemaTypesWithProps The allowed schema types with their properties.
     * @param AddFilter $wpService The WordPress service for adding filters.
     */
    public function __construct(
        private array $allowedSchemaTypesWithProps,
        private AddFilter $wpService
    ) {
    }

    /**
     * @inheritDoc
     */
    public function addHooks(): void
    {
        $this->wpService->addFilter('Municipio/SchemaData/SchemaTypes', [$this, 'filterSchemaTypes'], 10, 1);
        $this->wpService->addFilter('Municipio/SchemaData/SchemaProperties', [$this, 'filterSchemaProperties'], 10, 2);
    }

    /**
     * Filters the schema types based on the allowed schema types.
     *
     * @param array $schemaTypes The schema types to filter.
     *
     * @return array The filtered schema types.
     */
    public function filterSchemaTypes(array $schemaTypes): array
    {
        $allowedSchemaTypes = array_keys($this->allowedSchemaTypesWithProps);
        return array_filter($schemaTypes, fn ($schemaType) => in_array($schemaType, $allowedSchemaTypes));
    }

    /**
     * Filters the schema properties based on the allowed schema types and properties.
     *
     * @param array $schemaProperties The schema properties to filter.
     * @param mixed $schemaType The schema type.
     *
     * @return array The filtered schema properties.
     */
    public function filterSchemaProperties(array $schemaProperties, mixed $schemaType): array
    {
        if (!key_exists($schemaType, $this->allowedSchemaTypesWithProps)) {
            return $schemaProperties;
        }

        if (in_array('*', $this->allowedSchemaTypesWithProps[$schemaType])) {
            return $schemaProperties;
        }

        return array_filter($schemaProperties, fn ($propertyName) => in_array($propertyName, $this->allowedSchemaTypesWithProps[$schemaType]), ARRAY_FILTER_USE_KEY);
    }
}
