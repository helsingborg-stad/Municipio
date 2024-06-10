<?php

namespace Municipio\SchemaData\Utils;

interface GetSchemaTypeFromPostTypeInterface
{
    /**
     * Get the schema type from post type.
     *
     * @param string $postType The post type.
     *
     * @return string|null The schema type.
     */
    public function getSchemaTypeFromPostType(string $postType): ?string;
}
