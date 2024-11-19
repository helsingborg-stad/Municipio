<?php

namespace Municipio\PostObject\Renderer;

interface ConfigurableRendererInterface extends RendererInterface
{
    /**
     * Set the configuration for the renderer.
     *
     * @param array $config The configuration.
     */
    public function setConfig(array $config): void;

    /**
     * Get the configuration for the renderer.
     *
     * @return array The configuration.
     */
    public function getConfig(): array;
}
