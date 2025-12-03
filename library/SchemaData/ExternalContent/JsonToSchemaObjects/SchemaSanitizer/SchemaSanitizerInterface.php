<?php

namespace Municipio\SchemaData\ExternalContent\JsonToSchemaObjects\SchemaSanitizer;

use Municipio\Schema\BaseType;

interface SchemaSanitizerInterface
{
    public function sanitize(BaseType $schema): BaseType;
}
