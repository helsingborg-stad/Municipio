<?php

namespace Municipio\SchemaData\Helper;

class GetSchemaType
{
    private static ?array $postTypeSchemaTypes = null;

    public static function getSchemaTypesInUse(): array
    {
        if (is_null(self::$postTypeSchemaTypes)) {
            self::$postTypeSchemaTypes = get_field('post_type_schema_types', 'option');
        }

        return self::$postTypeSchemaTypes;
    }

    public static function getSchemaTypeFromPostType(string $postType): false|string
    {
        if (is_null(self::$postTypeSchemaTypes)) {
            self::getSchemaTypesInUse();
        }
       
        foreach (self::$postTypeSchemaTypes as $postAndSchemaTypeArray) {
            if ($postAndSchemaTypeArray['post_type'] === $postType) {
                return $postAndSchemaTypeArray['schema_type'];
            }
        }

        return false;
    }
}