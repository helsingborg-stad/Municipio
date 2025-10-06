<?php

namespace Municipio\SchemaData\ExternalContent\JsonToSchemaObjects;

use Municipio\SchemaData\ExternalContent\JsonToSchemaObjects\SchemaSanitizer\SchemaSanitizer;
use Municipio\SchemaData\SchemaPropertyValueSanitizer\SchemaPropertyValueSanitizer;
use Municipio\SchemaData\Utils\GetSchemaPropertiesWithParamTypes;

class JsonToSchemaObjectsFactory implements JsonToSchemaObjectsFactoryInterface {
    public static function create(): JsonToSchemaObjectsInterface
    {
        $jsonToSchemaObjects = new JsonToSchemaObjects();
        $jsonToSchemaObjects = new JsonConverterWithSanitizedProperties( new SchemaSanitizer(new SchemaPropertyValueSanitizer(), new GetSchemaPropertiesWithParamTypes()), $jsonToSchemaObjects );
        
        return $jsonToSchemaObjects;
    }
}