<?php

namespace Municipio\SchemaData\Acf\Utils;

use ReflectionClass;
use ReflectionMethod;
use Municipio\Schema\Schema;

/**
 * Class SchemaTypesFromSpatie
 *
 * @package Municipio\SchemaData\Acf\Utils
 */
class SchemaTypesFromSpatie implements GetSchemaTypesInterface
{
    /**
     * Get schema types from Spatie.
     *
     * @return array
     */
    public function getSchemaTypes(): array
    {
        $schemaClass = new ReflectionClass(Schema::class);
        $methods     = $schemaClass->getMethods(ReflectionMethod::IS_STATIC);

        return array_map(fn ($method) => ucfirst($method->name), $methods);
    }
}
