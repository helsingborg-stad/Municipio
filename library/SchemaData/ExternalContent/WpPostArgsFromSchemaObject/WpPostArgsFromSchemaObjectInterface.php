<?php

namespace Municipio\SchemaData\ExternalContent\WpPostArgsFromSchemaObject;

use Municipio\Schema\BaseType;

interface WpPostArgsFromSchemaObjectInterface
{
    /**
     * Create a array from a schema object to be used to insert/update a WP_Post.
     */
    public function transform(BaseType $schemaObject): array;
}
