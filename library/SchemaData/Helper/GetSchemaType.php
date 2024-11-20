<?php

namespace Municipio\SchemaData\Helper;

use AcfService\Contracts\GetField;

/**
 * Class GetSchemaType
 *
 * This class provides methods to retrieve schema types in use and retrieve the schema type from a given post type.
 */
class GetSchemaType
{
    private static ?GetField $acfService = null;

    /**
     * Sets the ACF service instance.
     *
     * @param GetField $acfService The ACF service instance.
     * @return void
     */
    public static function setAcfService(GetField $acfService): void
    {
        self::$acfService = $acfService;
    }

    /**
     * Retrieves the schema types in use.
     *
     * @return array The array of schema types in use.
     */
    public static function getSchemaTypesInUse(): array
    {
        if (is_null(self::$acfService)) {
            throw new \Exception('AcfService not set');
        }

        $schemaTypesInUse = self::$acfService->getField('post_type_schema_types', 'option') ?: [];
        if(is_array($schemaTypesInUse)) {
            return $schemaTypesInUse;
        }

        return [];
    }

    /**
     * Retrieves the schema type from a given post type.
     *
     * @param string $postType The post type to retrieve the schema type from.
     * @return false|string The schema type associated with the given post type, or false if not found.
     */
    public static function getSchemaTypeFromPostType(string $postType): false|string
    {
        $schemaTypesInUse = self::getSchemaTypesInUse();

        foreach ($schemaTypesInUse as $postAndSchemaTypeArray) {
            if ($postAndSchemaTypeArray['post_type'] === $postType) {
                return $postAndSchemaTypeArray['schema_type'];
            }
        }

        return false;
    }

    /**
     * Retrieves the post types associated with a given schema type.
     *
     * @param string $schemaType The schema type to retrieve the post types from.
     * @return array The post types associated with the given schema type.
     */
    public static function getPostTypesFromSchemaType(string $schemaType): array
    {
        $schemaTypesInUse = self::getSchemaTypesInUse();

        $postTypes = array_map(
            fn ($row) => $row['schema_type'] === $schemaType
            ? $row['post_type']
            : null,
            $schemaTypesInUse
        );

        return array_filter($postTypes);
    }
}
