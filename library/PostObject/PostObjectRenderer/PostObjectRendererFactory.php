<?php

namespace Municipio\PostObject\PostObjectRenderer;

use Municipio\PostObject\PostObjectRenderer\Appearances\Appearance;

/**
 * Factory for creating PostObjectRenderer instances.
 */
class PostObjectRendererFactory implements PostObjectRendererFactoryInterface
{
    /**
     * @inheritDoc
     */
    public static function create(Appearance $appearance, array $config = []): ?PostObjectRendererInterface
    {
        $instance = self::getAppearanceClassInstance($appearance);
        $instance->setConfig($config);

        return $instance;
    }

    /**
     * Get the class for the appearance.
     *
     * @param Appearance $appearance The appearance to get the class for.
     * @return PostObjectRendererInterface The class instance.
     */
    private static function getAppearanceClassInstance(Appearance $appearance): PostObjectRendererInterface
    {
        $class    = "Municipio\PostObject\PostObjectRenderer\Appearances\\{$appearance->value}";
        $instance = new $class();

        return $instance;
    }
}
