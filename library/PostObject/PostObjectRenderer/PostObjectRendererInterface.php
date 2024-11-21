<?php

namespace Municipio\PostObject\PostObjectRenderer;

use Municipio\PostObject\PostObjectInterface;

interface PostObjectRendererInterface
{
    /**
     * Get the rendered markup for the post object.
     */
    public function render(PostObjectInterface $postObject): string;

    /**
     * Set the view config.
     *
     * @param array $config The view config.
     */
    public function setConfig(array $config): void;

    /**
     * Get the view config.
     *
     * @return array The view config.
     */
    public function getConfig(): array;
}
