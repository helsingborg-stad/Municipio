<?php

namespace Municipio\PostObject\Renderer\PostObjectRenderer;

use Municipio\PostObject\PostObjectInterface;
use Municipio\PostObject\Renderer\ConfigurableRendererInterface;

interface PostObjectRendererInterface extends ConfigurableRendererInterface
{
    /**
     * Set the post object to render.
     *
     * @param PostObjectInterface $postObject The post object to render.
     */
    public function setPostObject(PostObjectInterface $postObject): void;

    /**
     * Get the post object to render.
     *
     * @return PostObjectInterface The post object to render.
     */
    public function getPostObject(): PostObjectInterface;
}
