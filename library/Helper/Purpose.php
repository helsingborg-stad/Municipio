<?php

namespace Municipio\Helper;

use Municipio\Helper\ElementAttribute as Element;

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
     * `getPurpose()` returns the value of the `options_purpose_X` option, where X is the type (most commonly a post type)
     *
     * @param string|WP_Post_Type type The type you want to get the purpose for.
     *
     * @return string|bool The value of the option with the key 'options_purpose_X'. Returns false if option is missing.
     */
    public static function getPurpose($type = null)
    {
        if (empty($type)) {
            $type = get_queried_object();
        }

        if (!is_string($type) && !is_a($type, 'WP_Post') && !is_a($type, 'WP_Post_Type')) {
            return false;
        }

        if (!is_string($type)) {
            if (is_a($type, 'WP_Post_Type')) {
                $type = $type->name;
            } elseif (is_a($type, 'WP_Post')) {
                $type = $type->post_type;
            }
        }

        $purpose = get_option('options_purpose_' . $type, false);

        return $purpose;
    }

    /**
     * If the elementType is not set, it will default to div. If the content is not set, it will
     * default to an empty string. Makes sure that the attributes are always set to a string, even if
     * they're empty/not set.
     * 
     * @param array $data This is an array of data that will be used to create the element.
     * 
     * @return array $data The data array is being returned.
     */
    public static function prepareContentSection(array $data): array
    {
        /* If the elementType is not set, it will default to div. */
        if (empty($data['elementType'])) {
            $data['elementType'] = 'div';
        }
        /* If the content is not set, it will default to an empty string. */
        if (empty($data['content'])) {
            $data['content'] = '';
        }

        /* Make sure that the attributes are always set to a string, even if they're empty. */
        if (!empty($data['attributeList']) && is_array($data['attributeList'])) {
            $data['attributes'] = Element::attributesToString($data['attributeList']);
        } else {
            $data['attributes'] = '';
        }

        return $data;
    }
}
