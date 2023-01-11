<?php

namespace Municipio\Helper;

class Purpose
{
    /**
     * Return an array containing key and label of all the purpose classes available in the templates folder.
     *
     * @return array An array of all the classes in the templates folder.
     */
    public static function getPurposes(): array
    {

        $purposes    = [];

        $purposePath = MUNICIPIO_PATH . 'templates';
        $recurse     = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($purposePath));
        $recurse->setMaxDepth(1);

        foreach ($recurse as $item) {
            if (1 !== $recurse->getDepth() || $recurse->isDot()) {
                continue;
            }
            if (!$item->isFile()) {
                continue;
            }

            $className = '\Municipio\Controller\\' . pathinfo($item->getFilename(), PATHINFO_FILENAME);
            if (!class_exists($className)) {
                require_once $item->getPathname();
            }
            if ('singular' === $className::getType()) {
                $purposes[$className::getKey()] = $className::getLabel();
            }
        }
        return $purposes;
    }
    /**
     * `getPurpose()` returns the value of the `options_purpose_X` option, where X is the post
     * type name
     *
     * @param string|WP_Post_Type postType The post type you want to get the purpose for.
     *
     * @return string|bool The value of the option with the key 'options_purpose_X'. Returns false if option is missing.
     */
    public static function getPurpose($postType = null)
    {

        if (!is_string($postType) || !is_a($postType, 'WP_Post') || !is_a($postType, 'WP_Post_Type')) {
            return;
        }

        if (empty($postType)) {
            $postType = get_queried_object();
        }

        if (is_a($postType, 'WP_Post_Type')) {
            $postType = $postType->name;
        } elseif (is_a($postType, 'WP_Post')) {
            $postType = $postType->post_type;
        } else {
            return false;
        }

        $purpose = get_option('options_purpose_' . $postType, '');
        if ('' === $purpose) {
            return false;
        }

        return $purpose;
    }
}
