<?php

namespace Municipio\ExternalContent\WpPostArgsFromSchemaObject;

use Municipio\ExternalContent\Sources\SourceInterface;
use Spatie\SchemaOrg\BaseType;

interface WpPostArgsFromSchemaObjectInterface
{
    /**
     * Create a array from a schema object to be used to insert/update a WP_Post.
     */
    public function create(BaseType $schemaObject, SourceInterface $source): array;
}
