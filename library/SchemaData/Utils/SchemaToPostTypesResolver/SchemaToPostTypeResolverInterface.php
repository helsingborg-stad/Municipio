<?php

namespace Municipio\SchemaData\Utils\SchemaToPostTypesResolver;

interface SchemaToPostTypeResolverInterface
{
    /**
     * Resolve the schema to a post type.
     *
     * @param string $schemaType The schema type.
     * @return array<string> An array of post types connected to the schema type.
     */
    public function resolve(string $schemaType): array;
}
