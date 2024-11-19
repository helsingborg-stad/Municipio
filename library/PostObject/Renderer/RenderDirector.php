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
        $renderedPostObjects = $this->renderPostObjects($postObjects, [$this, 'getBlockItemRender'], $config);

        return $this->builder
            ->setView('Collections.RendererPostObjectsWrappedWithOgrid')
            ->setConfig(['renderedPostObjects' => $renderedPostObjects])
            ->build();
    }

    /**
     * @inheritDoc
     */
    public function getBlockItemRender(PostObjectInterface $postObject, array $config = []): RenderInterface
    {
        $config = array_merge(
            [
            'gridColumnClass'    => null,
            'format'             => '12:16',
            'displayReadingTime' => false,
            'showPlaceholder'    => false,
            'postObject'         => $postObject
            ],
            $config
        );

        return $this->builder
            ->setView('Items.BlockItem')
            ->setConfig($config)
            ->build();
    }

    /**
     * @inheritDoc
     */
    public function getBoxGridItemCollectionRender(array $postObjects, array $config = []): RenderInterface
    {
        $renderedPostObjects = $this->renderPostObjects($postObjects, [$this, 'getBoxGridItemRender'], $config);

        return $this->builder
            ->setView('Collections.RendererPostObjectsWrappedWithOgrid')
            ->setConfig(['renderedPostObjects' => $renderedPostObjects])
            ->build();
    }

    /**
     * @inheritDoc
     */
    public function getBoxGridItemRender(PostObjectInterface $postObject, array $config = []): RenderInterface
    {
        $config = array_merge(['gridColumnClass' => null, 'ratio' => '1:1', 'postObject' => $postObject], $config);

        return $this->builder
            ->setView('Items.BoxGridItem')
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
            ->setView('Collections.ListItemCollection')
            ->setConfig(['renderedPostObjects' => $renderedPostObjects ])
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
            ->setView('Items.ListItem')
            ->setConfig(['postObject' => $postObject])
            ->build();
    }

    /**
     * @inheritDoc
     */
    public function getCardItemCollectionRender(array $postObjects, array $config = []): RenderInterface
    {
        $renderedCardItems = $this->renderPostObjects($postObjects, [$this, 'getCardItemRender'], $config);

        return $this->builder
            ->setView('Collections.RendererPostObjectsWrappedWithOgrid')
            ->setConfig(['renderedPostObjects' => $renderedCardItems])
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
            ->setView('Items.CardItem')
            ->setConfig($config)
            ->build();
    }

    /**
     * @inheritDoc
     */
    public function getCollectionItemCollectionRender(array $postObjects, array $config = []): RenderInterface
    {
        $renderedPostObjects = $this->renderPostObjects($postObjects, [$this, 'getCollectionItemRender'], $config);

        return $this->builder
            ->setView('Collections.CollectionItemCollection')
            ->setConfig(['renderedPostObjects' => $renderedPostObjects])
            ->build();
    }

    /**
     * @inheritDoc
     */
    public function getCollectionItemRender(PostObjectInterface $postObject, array $config = []): RenderInterface
    {
        $gridColumnClass = explode(' ', $config['gridColumnClass'] ?? '');
        $config          = array_merge(['displayFeaturedImage' => true], [...$config, 'gridColumnClass' => $gridColumnClass]);

        return $this->builder
            ->setView('CollectionItem')
            ->setConfig([...$config, 'postObject' => $postObject])
            ->build();
    }

    /**
     * @inheritDoc
     */
    public function getCompressedItemCollectionRender(array $postObjects, array $config = []): RenderInterface
    {
        $renderedPostObjects = $this->renderPostObjects($postObjects, [$this, 'getCompressedItemRender'], $config);

        return $this->builder
            ->setView('Collections.RendererPostObjectsWrappedWithOgrid')
            ->setConfig(['renderedPostObjects' => $renderedPostObjects])
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
            'postObject'         => $postObject
        ], $config);

        return $this->builder
            ->setView('Items.CompressedItem')
            ->setConfig($config)
            ->build();
    }

    /**
     * @inheritDoc
     */
    public function getNewsItemCollectionRender(array $postObjects, array $config = []): RenderInterface
    {
        $renderedPostObjects = $this->renderPostObjects($postObjects, [$this, 'getNewsItemRender'], $config);

        return $this->builder
            ->setView('Collections.NewsItemCollection')
            ->setConfig(['renderedPostObjects' => $renderedPostObjects])
            ->build();
    }

    /**
     * @inheritDoc
     */
    public function getNewsItemRender(PostObjectInterface $postObject, array $config = []): RenderInterface
    {
        $config = array_merge(['gridColumnClass' => null, 'postObject' => $postObject], $config);

        return $this->builder
            ->setView('Items.NewsItem')
            ->setConfig($config)
            ->build();
    }

    /**
     * @inheritDoc
     */
    public function getSchemaProjectItemCollectionRender(array $postObjects, array $config = []): RenderInterface
    {
        $renderedPostObjects = $this->renderPostObjects($postObjects, [$this, 'getSchemaProjectItemRender'], $config);

        return $this->builder
            ->setView('Collections.RendererPostObjectsWrappedWithOgrid')
            ->setConfig(['renderedPostObjects' => $renderedPostObjects])
            ->build();
    }

    /**
     * @inheritDoc
     */
    public function getSchemaProjectItemRender(PostObjectInterface $postObject, array $config = []): RenderInterface
    {
        $config = array_merge([ 'gridColumnClass' => null, 'showPlaceholder' => false, 'postObject' => $postObject ], $config);

        return $this->builder
            ->setView('Items.SchemaProjectItem')
            ->setConfig($config)
            ->build();
    }

    /**
     * @inheritDoc
     */
    public function getSegmentGridItemCollectionRender(array $postObjects, array $config = []): RenderInterface
    {
        $renderedPostObjects = $this->renderPostObjects($postObjects, [$this, 'getSegmentGridItemRender'], $config);

        return $this->builder
            ->setView('Collections.RendererPostObjectsWrappedWithOgrid')
            ->setConfig(['renderedPostObjects' => $renderedPostObjects])
            ->build();
    }

    /**
     * @inheritDoc
     */
    public function getSegmentGridItemRender(PostObjectInterface $postObject, array $config = []): RenderInterface
    {
        $config = array_merge(['gridColumnClass' => null, 'reverseColumns' => false, 'postObject' => $postObject], $config);

        return $this->builder
            ->setView('Items.SegmentGridItem')
            ->setConfig($config)
            ->build();
    }

    /**
     * @inheritDoc
     */
    public function getSegmentSliderItemRender(PostObjectInterface $postObject, array $config = []): RenderInterface
    {
        $config = array_merge(['postObject' => $postObject], $config);

        return $this->builder
            ->setView('Items.SegmentSliderItem')
            ->setConfig($config)
            ->build();
    }

    /**
     * @inheritDoc
     */
    public function getSegmentItemRender(PostObjectInterface $postObject, array $config = []): RenderInterface
    {
        $config = array_merge(['postObject' => $postObject], $config);

        return $this->builder
            ->setView('Items.SegmentItem')
            ->setConfig($config)
            ->build();
    }

    /**
     * @inheritDoc
     */
    public function getBoxSliderItemRender(PostObjectInterface $postObject, array $config = []): RenderInterface
    {
        $config = array_merge(['ratio' => '1:1', 'postObject' => $postObject], $config);

        return $this->builder
            ->setView('Items.BoxSliderItem')
            ->setConfig($config)
            ->build();
    }

    /**
     * @inheritDoc
     */
    public function getBoxItemRender(PostObjectInterface $postObject, array $config = []): RenderInterface
    {
        $config = array_merge(['ratio' => '1:1', 'postObject' => $postObject], $config);

        return $this->builder
            ->setView('Items.BoxItem')
            ->setConfig($config)
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
