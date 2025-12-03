<?php

namespace Municipio\PostObject\Icon;

interface IconFactoryInterface
{
    /**
     * Create an icon.
     *
     * @param array $properties Icon properties.
     *
     * @return IconInterface
     */
    public static function create(array $properties = []): IconInterface;
}
