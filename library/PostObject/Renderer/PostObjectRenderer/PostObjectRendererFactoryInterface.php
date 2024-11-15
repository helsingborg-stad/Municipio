<?php

namespace Municipio\PostObject\Renderer\PostObjectRenderer;

interface PostObjectRendererFactoryInterface
{
    /**
     * Get instance of renderer.
     *
     * @param PostObjectRendererType $type
     * @return PostObjectRendererInterface
     */
    public function create(PostObjectRendererType $type): PostObjectRendererInterface;
}
