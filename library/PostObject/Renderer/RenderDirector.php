<?php

namespace Municipio\PostObject\Renderer;

use Municipio\PostObject\PostObjectInterface;

/**
 * Director for creating Render.
 */
class RenderDirector implements RenderDirectorInterface
{
    /**
     * Constructor.
     */
    public function __construct(private RenderBuilderInterface $builder)
    {
    }

    /**
     * @inheritDoc
     */
    public function getBlockItemCollectionRender(array $postObjects, array $config = []): RenderInterface
    {
        $renderedPostObjects = $this->renderPostObjects($postObjects, [$this, 'getBlockItemRender']);

        return $this->builder
            ->setView('BlockItemCollection')
            ->setConfig(['renderedBlockItems' => $renderedPostObjects])
            ->build();
    }

    /**
     * @inheritDoc
     */
    public function getBlockItemRender(PostObjectInterface $postObject, array $config = []): RenderInterface
    {
        return $this->builder
            ->setView('BlockItem')
            ->setConfig(['postObject' => $postObject])
            ->build();
    }

    /**
     * @inheritDoc
     */
    public function getBoxGridItemCollectionRender(array $postObjects, array $config = []): RenderInterface
    {
        $config              = array_merge(['gridColumnClass' => null, 'ratio' => '1:1'], $config);
        $renderedPostObjects = $this->renderPostObjects($postObjects, [$this, 'getBoxGridItemRender'], $config);

        return $this->builder
            ->setView('BoxGridItemCollection')
            ->setConfig(['renderedBoxGridItems' => $renderedPostObjects])
            ->build();
    }

    /**
     * @inheritDoc
     */
    public function getBoxGridItemRender(PostObjectInterface $postObject, array $config = []): RenderInterface
    {
        $config = array_merge(['gridColumnClass' => null, 'ratio' => '1:1', 'postObject' => $postObject], $config);

        return $this->builder
            ->setView('BoxGridItem')
            ->setConfig($config)
            ->build();
    }

    /**
     * @inheritDoc
     */
    public function getListItemCollectionRender(array $postObjects, array $config = []): RenderInterface
    {
        $renderedPostObjects = $this->renderPostObjects($postObjects, [$this, 'getListItemRender']);

        return $this->builder
            ->setView('ListItemCollection')
            ->setConfig(['renderedListItems' => $renderedPostObjects ])
            ->build();
    }

    /**
     * Get ListItem render.
     *
     * @param PostObjectInterface $postObject
     * @return RenderInterface
     */
    public function getListItemRender(PostObjectInterface $postObject, array $config = []): RenderInterface
    {
        return $this->builder
            ->setView('ListItem')
            ->setConfig(['postObject' => $postObject])
            ->build();
    }

    /**
     * @inheritDoc
     */
    public function getCardItemCollectionRender(array $postObjects, array $config = []): RenderInterface
    {
        $config = array_merge([
            'displayReadingTime' => false,
            'gridColumnClass'    => null,
            'showPlaceholder'    => false,
            'renderedCardItems'  => $this->renderPostObjects($postObjects, [$this, 'getCardItemRender'], $config)
        ], $config);

        $renderedCardItems = $this->renderPostObjects($postObjects, [$this, 'getCardItemRender'], $config);

        return $this->builder
            ->setView('CardItemCollection')
            ->setConfig([...$config, 'renderedCardItems' => $renderedCardItems])
            ->build();
    }

    /**
     * @inheritDoc
     */
    public function getCardItemRender(PostObjectInterface $postObject, array $config = []): RenderInterface
    {
        $config = array_merge([
            'displayReadingTime' => false,
            'gridColumnClass'    => null,
            'showPlaceholder'    => false,
            'postObject'         => $postObject
        ], $config);

        return $this->builder
            ->setView('CardItem')
            ->setConfig($config)
            ->build();
    }

    /**
     * @inheritDoc
     */
    public function getCollectionItemCollectionRender(array $postObjects, array $config = []): RenderInterface
    {
        $renderedPostObjects = $this->renderPostObjects($postObjects, [$this, 'getCollectionItemRender']);

        return $this->builder
            ->setView('CollectionItemCollection')
            ->setConfig(['renderedCollectionItems' => $renderedPostObjects])
            ->build();
    }

    /**
     * @inheritDoc
     */
    public function getCollectionItemRender(PostObjectInterface $postObject, array $config = []): RenderInterface
    {
        return $this->builder
            ->setView('CollectionItem')
            ->setConfig(['postObject' => $postObject, 'gridColumnClass' => []])
            ->build();
    }

    /**
     * @inheritDoc
     */
    public function getCompressedItemCollectionRender(array $postObjects, array $config = []): RenderInterface
    {
        $config = array_merge([
            'displayReadingTime'      => false,
            'gridColumnClass'         => null,
            'showPlaceholder'         => false,
            'renderedCompressedItems' => $this->renderPostObjects($postObjects, [$this, 'getCompressedItemRender'], $config)
        ], $config);

        return $this->builder
            ->setView('CompressedItemCollection')
            ->setConfig($config)
            ->build();
    }

    /**
     * @inheritDoc
     */
    public function getCompressedItemRender(PostObjectInterface $postObject, array $config = []): RenderInterface
    {
        $config = array_merge([
            'displayReadingTime' => false,
            'gridColumnClass'    => null,
            'showPlaceholder'    => false,
        ], $config);

        return $this->builder
            ->setView('CompressedItem')
            ->setConfig(['postObject' => $postObject])
            ->build();
    }

    /**
     * @inheritDoc
     */
    public function getNewsItemCollectionRender(array $postObjects, array $config = []): RenderInterface
    {
        $config              = array_merge(['gridColumnClass' => null], $config);
        $renderedPostObjects = $this->renderPostObjects($postObjects, [$this, 'getNewsItemRender'], $config);

        return $this->builder
            ->setView('NewsItemCollection')
            ->setConfig(['renderedNewsItems' => $renderedPostObjects])
            ->build();
    }

    /**
     * @inheritDoc
     */
    public function getNewsItemRender(PostObjectInterface $postObject, array $config = []): RenderInterface
    {
        $config = array_merge(['gridColumnClass' => null, 'postObject' => $postObject], $config);

        return $this->builder
            ->setView('NewsItem')
            ->setConfig($config)
            ->build();
    }

    /**
     * @inheritDoc
     */
    public function getSchemaProjectItemCollectionRender(array $postObjects, array $config = []): RenderInterface
    {
        $config              = array_merge(['gridColumnClass' => null, 'showPlaceholder' => false], $config);
        $renderedPostObjects = $this->renderPostObjects($postObjects, [$this, 'getSchemaProjectItemRender'], $config);

        return $this->builder
            ->setView('SchemaProjectItemCollection')
            ->setConfig(['renderedSchemaProjectItems' => $renderedPostObjects])
            ->build();
    }

    /**
     * @inheritDoc
     */
    public function getSchemaProjectItemRender(PostObjectInterface $postObject, array $config = []): RenderInterface
    {
        $config = array_merge([ 'gridColumnClass' => null, 'showPlaceholder' => false, 'postObject' => $postObject ], $config);

        return $this->builder
            ->setView('SchemaProjectItem')
            ->setConfig($config)
            ->build();
    }

    /**
     * @inheritDoc
     */
    public function getSegmentGridItemCollectionRender(array $postObjects, array $config = []): RenderInterface
    {
        $renderedPostObjects = $this->renderPostObjects($postObjects, [$this, 'getSegmentGridItemRender']);

        return $this->builder
            ->setView('SegmentGridItemCollection')
            ->setConfig(['renderedSegmentGridItems' => $renderedPostObjects])
            ->build();
    }

    /**
     * @inheritDoc
     */
    public function getSegmentGridItemRender(PostObjectInterface $postObject, array $config = []): RenderInterface
    {
        return $this->builder
            ->setView('SegmentGridItem')
            ->setConfig(['postObject' => $postObject])
            ->build();
    }

    /**
     * Render post objects.
     *
     * @param PostObjectInterface[] $postObjects
     * @param callable $renderFunction
     * @return string
     */
    public function renderPostObjects(array $postObjects, callable $renderFunction, array $config = []): string
    {
        $renderers           = array_map(fn ($postObject) => call_user_func($renderFunction, $postObject, $config), $postObjects);
        $renderedPostObjects = array_map(fn (RenderInterface $render) => $render->render(), $renderers);

        return implode('', $renderedPostObjects);
    }
}
