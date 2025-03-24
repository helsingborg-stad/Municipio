<?php

namespace Municipio\SchemaData;

use Municipio\HooksRegistrar\Hookable;
use WpService\Contracts\AddFilter;

class LimitSchemaTypesAndProperties implements Hookable
{
    public function __construct(
        private array $allowedSchemaTypesWithProps,
        private AddFilter $wpService
    ) {
    }

    public function addHooks(): void
    {
        $this->wpService->addFilter('Municipio/SchemaData/SchemaTypes', [$this, 'filterSchemaTypes'], 10, 1);
        $this->wpService->addFilter('Municipio/SchemaData/SchemaProperties', [$this, 'filterSchemaProperties'], 10, 2);
    }

    public function filterSchemaTypes(array $schemaTypes): array
    {
        $allowedSchemaTypes = array_keys($this->allowedSchemaTypesWithProps);
        return array_filter($schemaTypes, fn ($schemaType) => in_array($schemaType, $allowedSchemaTypes));
    }

    public function filterSchemaProperties(array $schemaProperties, mixed $schemaType): array
    {
        if (!key_exists($schemaType, $this->allowedSchemaTypesWithProps)) {
            return $schemaProperties;
        }

        if( in_array('*', $this->allowedSchemaTypesWithProps[$schemaType]) ){
            return $schemaProperties;
        }

        return array_filter($schemaProperties, fn ($propertyName) => in_array($propertyName, $this->allowedSchemaTypesWithProps[$schemaType]), ARRAY_FILTER_USE_KEY);
    }
}
