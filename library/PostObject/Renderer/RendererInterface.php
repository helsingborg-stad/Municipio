<?php

namespace Municipio\PostObject\Renderer;

interface RendererInterface
{
    /**
     * Get the rendered markup.
     */
    public function render(): string;
}
