<?php

namespace Municipio\PostObject\PostObjectRenderer;

use Municipio\PostObject\PostObjectInterface;

interface PostObjectRendererInterface
{
    /**
     * Get the rendered markup for the post object.
     */
    public function render(PostObjectInterface $postObject): string;
}
