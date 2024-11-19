<?php

namespace Municipio\PostObject\Renderer\PostObjectRenderer;

use Municipio\PostObject\PostObjectInterface;
use Municipio\PostObject\Renderer\ConfigurableRenderer;
use Municipio\PostObject\Renderer\PostObjectRenderer\PostObjectRendererInterface;

/**
 * Render PostObject as a list item.
 */
abstract class PostObjectRenderer extends ConfigurableRenderer implements PostObjectRendererInterface
{
    protected PostObjectInterface $postObject;

    /**
     * @inheritDoc
     */
    public function setPostObject(PostObjectInterface $postObject): void
    {
        $this->postObject = $postObject;
    }

    /**
     * @inheritDoc
     */
    public function getPostObject(): PostObjectInterface
    {
        return $this->postObject;
    }

    /**
     * Get the view paths.
     *
     * @return array The view paths.
     */
    public function getViewPaths(): array
    {
        return [__DIR__ . '/Views/'];
    }

    /**
     * Get the view data.
     */
    public function getViewData(): array
    {
        return ['postObject' => $this->getPostObject(), 'config' => $this->getConfig(), 'lang' => $this->getLanguageObject()];
    }

    /**
     * Get the view name.
     * @return string The view name.
     */
    abstract public function getViewName(): string;
}
