<?php

namespace Municipio\PostObject\Renderer;

/**
 * Builder for creating Render.
 */
class RenderBuilder implements RenderBuilderInterface
{
    private array $config = [];
    private string $view  = '';

    /**
     * @inheritDoc
     */
    public function setConfig(array $config): RenderBuilderInterface
    {
        $this->config = $config;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setView(string $view): RenderBuilderInterface
    {
        $this->view = $view;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function build(): RenderInterface
    {
        return new Render($this->view, $this->config);
    }
}
