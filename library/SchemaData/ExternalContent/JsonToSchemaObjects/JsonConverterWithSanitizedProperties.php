<?php

namespace Municipio\SchemaData\ExternalContent\JsonToSchemaObjects;

use Municipio\Schema\BaseType;
use Municipio\SchemaData\ExternalContent\JsonToSchemaObjects\SchemaSanitizer\SchemaSanitizerInterface;

class JsonConverterWithSanitizedProperties implements JsonToSchemaObjectsInterface {
    
    public function __construct(
        private SchemaSanitizerInterface $sanitizer, 
        private JsonToSchemaObjectsInterface $innerConverter)
    {
    }

    public function transform(string $json): array
    {
        $objects = $this->innerConverter->transform($json);
        return array_map(fn(BaseType $object) => $this->sanitizer->sanitize($object), $objects);
    }
}