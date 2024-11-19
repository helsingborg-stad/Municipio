<?php

namespace Municipio\PostObject\Renderer\PostObjectCollectionRenderer;

use Municipio\PostObject\PostObjectInterface;
use Municipio\PostObject\Renderer\ConfigurableRenderer;
use Municipio\PostObject\Renderer\PostObjectRenderer\PostObjectRendererFactory;
use Municipio\PostObject\Renderer\PostObjectRenderer\PostObjectRendererType;

/**
 * Render PostObject as a list item.
 */
abstract class PostObjectCollectionRenderer extends ConfigurableRenderer implements PostObjectCollectionRendererInterface
{
    /**
     * Post objects to render.
     * @var PostObjectInterface[]
     */
    protected array $postObjects;

    /**
     * @inheritDoc
     */
    public function setPostObjects(array $postObjects): void
    {
        $this->postObjects = $postObjects;
    }

    /**
     * @inheritDoc
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
        return ['renderedPostObjects' => $this->getRenderedPostObjects(), 'config' => $this->getConfig(), 'lang' => $this->getLanguageObject()];
    }

    /**
     * Get the view name.
     * @return string The view name.
     */
    abstract public function getViewName(): string;

    /**
     * @inheritDoc
     */
    public function getRenderedPostObjects(): string
    {
        $renderer = (new PostObjectRendererFactory())->create($this->getPostObjectRendererType());
        $renderer->setConfig($this->getConfig());

        $renderedPostObjects = array_map(function ($postObject) use ($renderer) {
            $renderer->setPostObject($postObject);
            return $renderer->render($postObject);
        }, $this->postObjects);

        return implode('', $renderedPostObjects);
    }

    /**
     * Get the post object renderer type.
     *
     * @return PostObjectRendererType The post object renderer type.
     */
    abstract public function getPostObjectRendererType(): PostObjectRendererType;
}
