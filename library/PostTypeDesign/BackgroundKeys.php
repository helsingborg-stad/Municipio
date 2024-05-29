<?php

namespace Municipio\PostTypeDesign;

/**
 * Class BackgroundKeys
 *
 * This class implements the KeysInterface and provides a method to get an array of background keys.
 */
class BackgroundKeys implements KeysInterface
{
    /**
     * Get the background keys.
     *
     * @return array An array of background keys.
     */
    public static function get(): array
    {
        return [
            'footer_background'
        ];
    }
}
