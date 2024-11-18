<?php

namespace Municipio\PostObject\Renderer;

interface RenderInterface
{
    /**
     * Render the view.
     *
     * @return string
     */
    public function render(): string;
}
