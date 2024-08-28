<?php

namespace Municipio\Config\Features\SchemaData\Contracts;

interface TryGetSchemaTypeFromPostType
{
    /**
     * Get schema type from post type.
     *
     * @param string $postType Post type.
     *
     * @return string|null Schema type. Null if not found.
     */
    public function tryGetSchemaTypeFromPostType(string $postType): ?string;
}
