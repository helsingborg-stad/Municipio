<?php

namespace Municipio\SchemaData\Helper;

class GetSchemaType
{
    private static ?array $schemaTypesInUse = null;

    public static function getSchemaTypesInUse(): array
    {
        if (is_null(self::$schemaTypesInUse)) {
            self::$schemaTypesInUse = get_field('post_type_schema_types', 'option');
        }

        return self::$schemaTypesInUse;
    }

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