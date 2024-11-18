<?php

namespace Municipio\PostObject\Renderer;

interface RenderBuilderInterface
{
    /**
     * Set config.
     *
     * @param array $config
     * @return RenderBuilderInterface
     */
    public function setConfig(array $config): RenderBuilderInterface;

    /**
     * Set view.
     *
     * @param string $view
     * @return RenderBuilderInterface
     */
    public function setView(string $view): RenderBuilderInterface;

    /**
     * Get renderer instance.
     *
     * @return RenderInterface
     */
    public function build(): RenderInterface;
}
