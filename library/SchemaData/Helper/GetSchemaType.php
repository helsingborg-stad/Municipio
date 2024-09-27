<?php

namespace Municipio\SchemaData\Helper;

class GetSchemaType
{
    private static ?array $schemaTypesInUse = null;

    /**
     * Retrieves the schema types in use.
     *
     * @return array The array of schema types in use.
     */
    public static function getSchemaTypesInUse(): array
    {
        if (is_null(self::$schemaTypesInUse)) {
            self::$schemaTypesInUse = get_field('post_type_schema_types', 'option');
        }

        return self::$schemaTypesInUse;
    }

    /**
     * Retrieves the schema type from a given post type.
     *
     * @param string $postType The post type to retrieve the schema type from.
     * @return false|string The schema type associated with the given post type, or false if not found.
     */
    public static function getSchemaTypeFromPostType(string $postType): false|string
    {
        if (is_null(self::$schemaTypesInUse)) {
            self::getSchemaTypesInUse();
        }
       
        foreach (self::$schemaTypesInUse as $postAndSchemaTypeArray) {
            if ($postAndSchemaTypeArray['post_type'] === $postType) {
                return $postAndSchemaTypeArray['schema_type'];
            }
        }

        return false;
    }
}