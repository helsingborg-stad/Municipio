<?php

namespace Municipio\Helper;

/**
 * Archive class.
 *
 * This class is responsible for handling archive related functionality.
 * It is located in the file /workspaces/municipio-deployment/wp-content/themes/municipio/library/Helper/Archive.php.
 */
class Archive
{
    /**
     * Get the template style for this archive
     *
     * @param string $postType  The post type to get the option from
     * @param string $default   The default value, if not found.
     * @param string $postType  The post type to get the option from
     *
     * @return string
     */
    public static function getTemplate($args, string $default = 'cards', $postType = null): string
    {
        $schemaKey         = 'schema';
        $archiveAppearance = $default;

        if (empty($args->style)) {
            return $archiveAppearance;
        }

        $archiveAppearance = $args->style;

        if ($postType && $archiveAppearance === $schemaKey) {
            $schemaType = \Municipio\SchemaData\Helper\GetSchemaType::getSchemaTypeFromPostType($postType);

            if ($schemaType) {
                $archiveAppearance = $schemaKey . '-' . lcfirst($schemaType);
            }
        }

        return $archiveAppearance;
    }
}
