<?php

namespace Municipio\SchemaData\Utils\SchemaToPostTypesResolver;

use Generator;

interface SchemaToPostTypeResolverInterface
{
    /**
     * Resolve the schema to a post type.
     *
     * @param string $schemaType The schema type.
     * @return Generator<string> A generator yielding post types connected to the schema type.
     */
    public function resolve(string $schemaType): Generator;
}
