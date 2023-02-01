<?php

namespace Municipio\Helper;

use Municipio\Helper\Controller as ControllerHelper;

class Purpose
{
    /**
     * ! WIP
     * ! This method will need to be rebuilt when purposes are moved to library/Controller/Purpose
     * Return an array containing key and label of all the purposes available in the registered controller directories.
     *
     * @return array An array of all the classes .
     */
    public static function getPurposes(): array
    {
        $purposes    = [];
        foreach (ControllerHelper::getControllerPaths() as $path) {
            if (is_dir($purposePath = $path . DIRECTORY_SEPARATOR . 'Purpose')) {
                $recurse = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($purposePath));
                $recurse->setMaxDepth(1);

                foreach ($recurse as $item) {
                    if (1 !== $recurse->getDepth() || $recurse->isDot() || !$item->isFile()) {
                        continue;
                    }

                    require_once $item->getPathname();

                    $purpose = ControllerHelper::getNamespace($item->getPathname()) . ControllerHelper::camelCase(pathinfo($item->getFilename(), PATHINFO_FILENAME));

                    $purposes[$purpose::getKey()] = $purpose::getLabel();
                }
            }
        }
        return $purposes;
    }
    /**
     * `getPurpose(X)` returns the value of the `options_purpose_X` option
     *
     * @param string|WP_Post|WP_Post_Type type The type you want to get the purpose for.
     * @return string The value of the option with the key 'options_purpose_X'.
     */
    public static function getPurpose($type = ''): string
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
