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
            if (1 !== $recurse->getDepth() || $recurse->isDot() || !$item->isFile()) {
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
     * `getPurpose()` returns the value of the `options_purpose_X` option, where X is the type (most commonly a post type)
     *
     * @param string|WP_Post_Type type The type you want to get the purpose for.
     *
     * @return string|bool The value of the option with the key 'options_purpose_X'. Returns false if option is missing.
     */
    public static function getPurpose(string $type = ''): string
    {
        if ('' === $type) {
            $type = get_queried_object();
        }

        if (!is_string($type) && !is_a($type, 'WP_Post') && !is_a($type, 'WP_Post_Type')) {
            return '';
        }

        if (is_a($type, 'WP_Post')) {
            $type = $type->post_type;
        } elseif (is_a($type, 'WP_Post_Type')) {
            $type = $type->name;
        }

        return is_string($type) ? get_option('options_purpose_' . $type, '') : '';
    }
    /**
     * If the purpose is not empty, return true, otherwise return false.
     *
     * @return bool A boolean value.
     */
    public static function hasPurpose(string $type = ''): bool
    {
        return (bool) self::getPurpose($type);
    }
}
