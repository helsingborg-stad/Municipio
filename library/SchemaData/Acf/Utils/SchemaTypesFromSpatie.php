<?php

namespace Municipio\SchemaData\Acf\Utils;

use ReflectionClass;
use ReflectionMethod;
use Spatie\SchemaOrg\Schema;

class SchemaTypesFromSpatie implements GetSchemaTypesInterface
{
    public function getSchemaTypes(): array
    {
        $schemaClass = new ReflectionClass(Schema::class);
        $methods     = $schemaClass->getMethods(ReflectionMethod::IS_STATIC);

        return array_map(fn ($method) => ucfirst($method->name), $methods);
    }
}
