<?php

namespace Municipio\PostObject\Renderer\PostObjectCollectionRenderer;

use Municipio\PostObject\PostObjectInterface;
use Municipio\PostObject\Renderer\ConfigurableRendererInterface;

/**
 * Render PostObject as a list item.
 */
interface PostObjectCollectionRendererInterface extends ConfigurableRendererInterface
{
    /**
     * Set post objects to render.
     *
     * @param PostObjectInterface[] $postObjects
     */
    public function setPostObjects(array $postObjects): void;

    /**
     * Get post objects to render.
     *
     * @return string
     */
    public function getRenderedPostObjects(): string;
}
