<?php

namespace Municipio\SchemaData\ExternalContent\JsonToSchemaObjects\SchemaSanitizer\Mappers;

use Municipio\Schema\BaseType;

interface MapperInterface
{
    public function map(BaseType $schema): BaseType;
}