<?php

namespace Municipio\PostObject\PostObjectRenderer;

use Municipio\PostObject\PostObjectRenderer\Appearances\Appearance;

interface PostObjectRendererFactoryInterface
{
    /**
     * Create a new PostObjectRenderer instance.
     *
     * @param Appearance $appearance The appearance to create a renderer for.
     * @param array $config The configuration for the renderer.
     * @return PostObjectRendererInterface|null The created instance or null if the appearance is not supported.
     */
    public static function create(Appearance $appearance, array $config = []): ?PostObjectRendererInterface;
}
