<?php

namespace Modularity\Helper;

/**
 * Helper class for options
 */
class Options {

    /**
     * Get list of currently available archives slugs that has a template
     * @return array
     */
    public static function getSingleTemplateSlugs()
    {
        $postTypeNames = self::getPublicPostTypeNames();
        return self::getSingleTemplatesFromPostTypeNames($postTypeNames);
    }

    public static function getArchiveTemplateSlugs()
    {
        $postTypeNames = self::getPostTypesWithArchives();
        return self::getArchiveTemplatesFromPostTypeNames($postTypeNames);
    }

    private static function getSingleTemplatesFromPostTypeNames(array $postTypeNames):array
    {
        return self::getTemplatesFromPostTypeNames($postTypeNames, 'single');
    }

    private static function getArchiveTemplatesFromPostTypeNames(array $postTypeNames): array
    {
        return self::getTemplatesFromPostTypeNames($postTypeNames, 'archive');
    }

    private static function getTemplatesFromPostTypeNames(array $postTypeNames, string $templateType): array
    {
        $templates = array_map(function ($postTypeName) use ($templateType) {
            $template = \Modularity\Helper\Wp::findCoreTemplates([$templateType . '-' . $postTypeName]);
            return ($template)
                ? $template
                : $templateType;
        }, $postTypeNames);

        return array_unique($templates);
    }

    private static function getPostTypesWithArchives() {
        return get_post_types(array(
            'has_archive' => true
        ), 'names');
    }

    private static function getPublicPostTypeNames() {
        return get_post_types(array(
            'public' => true,
            'show_ui' => true,
        ), 'names');
    }
}