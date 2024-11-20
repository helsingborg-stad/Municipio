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
    public function getBlockItemCollectionRender(array $config = []): RenderInterface
    {
        $config = array_merge([
            'gridColumnClass'    => null,
            'format'             => '12:16',
            'displayReadingTime' => false,
            'showPlaceholder'    => false
        ], $config);

        return $this->builder
            ->setView('Collections.BlockItemCollection')
            ->setConfig($config)
            ->build();
    }

    /**
     * @inheritDoc
     */
    public function getBlockItemRender(array $config = []): RenderInterface
    {
        $config = array_merge([
            'gridColumnClass'    => null,
            'format'             => '12:16',
            'displayReadingTime' => false,
            'showPlaceholder'    => false,
            ], $config);

        return $this->builder
            ->setView('Items.BlockItem')
            ->setConfig($config)
            ->build();
    }

    /**
     * @inheritDoc
     */
    public function getBoxGridItemCollectionRender(array $config = []): RenderInterface
    {
        $config = array_merge([
            'gridColumnClass' => null,
            'noGutter'        => false,
            'ratio'           => '1:1',
            'stretch'         => false,
        ], $config);

        return $this->builder
            ->setView('Collections.BoxGridItemCollection')
            ->setConfig($config)
            ->build();
    }

    /**
     * @inheritDoc
     */
    public function getBoxGridItemRender(array $config = []): RenderInterface
    {
        $config = array_merge([
            'gridColumnClass' => null,
            'ratio'           => '1:1'
        ], $config);

        return $this->builder
            ->setView('Items.BoxGridItem')
            ->setConfig($config)
            ->build();
    }

    /**
     * @inheritDoc
     */
    public function getListItemCollectionRender(array $config = []): RenderInterface
    {
        $config = ['title' => null, ...$config];

        return $this->builder
            ->setView('Collections.ListItemCollection')
            ->setConfig($config)
            ->build();
    }

    /**
     * Get ListItem render.
     *
     * @param PostObjectInterface $postObject
     * @return RenderInterface
     */
    public function getListItemRender(array $config = []): RenderInterface
    {
        return $this->builder
            ->setView('Items.ListItem')
            ->setConfig($config)
            ->build();
    }

    /**
     * @inheritDoc
     */
    public function getCardItemCollectionRender(array $config = []): RenderInterface
    {
        $config = array_merge([
            'displayReadingTime' => false,
            'gridColumnClass'    => null,
            'showPlaceholder'    => false
        ], $config);

        return $this->builder
            ->setView('Collections.CardItemCollection')
            ->setConfig($config)
            ->build();
    }

    /**
     * @inheritDoc
     */
    public function getCardItemRender(array $config = []): RenderInterface
    {
        $config = array_merge([
            'displayReadingTime' => false,
            'gridColumnClass'    => null,
            'showPlaceholder'    => false
        ], $config);

        return $this->builder
            ->setView('Items.CardItem')
            ->setConfig($config)
            ->build();
    }

    /**
     * @inheritDoc
     */
    public function getCollectionItemCollectionRender(array $config = []): RenderInterface
    {
        $config                    = array_merge(['displayFeaturedImage' => true], $config);
        $config['gridColumnClass'] = explode(' ', $config['gridColumnClass'] ?? '');

        return $this->builder
            ->setView('Collections.CollectionItemCollection')
            ->setConfig($config)
            ->build();
    }

    /**
     * @inheritDoc
     */
    public function getCollectionItemRender(array $config = []): RenderInterface
    {
        $config                    = array_merge(['displayFeaturedImage' => true], $config);
        $config['gridColumnClass'] = explode(' ', $config['gridColumnClass'] ?? '');

        return $this->builder
            ->setView('CollectionItem')
            ->setConfig($config)
            ->build();
    }

    /**
     * @inheritDoc
     */
    public function getCompressedItemCollectionRender(array $config = []): RenderInterface
    {
        $config = array_merge([
            'displayReadingTime' => false,
            'gridColumnClass'    => null,
            'showPlaceholder'    => false,
            'postObjectView'     => 'Items.CompressedItem'
        ], $config);

        return $this->builder
            ->setView('Collections.RendererPostObjectsWrappedWithOgrid')
            ->setConfig($config)
            ->build();
    }

    /**
     * @inheritDoc
     */
    public function getCompressedItemRender(array $config = []): RenderInterface
    {
        $config = array_merge([
            'displayReadingTime' => false,
            'gridColumnClass'    => null,
            'showPlaceholder'    => false,
        ], $config);

        return $this->builder
            ->setView('Items.CompressedItem')
            ->setConfig($config)
            ->build();
    }

    /**
     * @inheritDoc
     */
    public function getNewsItemCollectionRender(array $config = []): RenderInterface
    {
        $config = array_merge(['gridColumnClass' => null], $config);

        return $this->builder
            ->setView('Collections.NewsItemCollection')
            ->setConfig($config)
            ->build();
    }

    /**
     * @inheritDoc
     */
    public function getNewsItemRender(array $config = []): RenderInterface
    {
        $config = array_merge(['gridColumnClass' => null], $config);

        return $this->builder
            ->setView('Items.NewsItem')
            ->setConfig($config)
            ->build();
    }

    /**
     * @inheritDoc
     */
    public function getSchemaProjectItemCollectionRender(array $config = []): RenderInterface
    {
        $config = array_merge([
            'gridColumnClass' => null,
            'showPlaceholder' => false,
            'postObjectView'  => 'Items.SchemaProjectItem'
        ], $config);

        return $this->builder
            ->setView('Collections.RendererPostObjectsWrappedWithOgrid')
            ->setConfig($config)
            ->build();
    }

    /**
     * @inheritDoc
     */
    public function getSchemaProjectItemRender(array $config = []): RenderInterface
    {
        $config = array_merge([
            'gridColumnClass' => null,
            'showPlaceholder' => false
        ], $config);

        return $this->builder
            ->setView('Items.SchemaProjectItem')
            ->setConfig($config)
            ->build();
    }

    /**
     * @inheritDoc
     */
    public function getSegmentGridItemCollectionRender(array $config = []): RenderInterface
    {
        $config = array_merge([
            'gridColumnClass' => null,
            'reverseColumns'  => false,
            'postObjectView'  => 'Items.SegmentGridItem'
        ], $config);

        return $this->builder
            ->setView('Collections.RendererPostObjectsWrappedWithOgrid')
            ->setConfig($config)
            ->build();
    }

    /**
     * @inheritDoc
     */
    public function getSegmentGridItemRender(array $config = []): RenderInterface
    {
        $config = array_merge([
            'gridColumnClass' => null,
            'reverseColumns'  => false,
        ], $config);

        return $this->builder
            ->setView('Items.SegmentGridItem')
            ->setConfig($config)
            ->build();
    }

    /**
     * @inheritDoc
     */
    public function getSegmentSliderItemRender(array $config = []): RenderInterface
    {
        $config = array_merge(['postObject' => $config['postObject'] ?? null], $config);

        return $this->builder
            ->setView('Items.SegmentSliderItem')
            ->setConfig($config)
            ->build();
    }

    /**
     * @inheritDoc
     */
    public function getSegmentItemRender(array $config = []): RenderInterface
    {
        return $this->builder
            ->setView('Items.SegmentItem')
            ->setConfig($config)
            ->build();
    }

    /**
     * @inheritDoc
     */
    public function getBoxSliderItemRender(array $config = []): RenderInterface
    {
        $config = array_merge(['ratio' => '1:1'], $config);

        return $this->builder
            ->setView('Items.BoxSliderItem')
            ->setConfig($config)
            ->build();
    }

    /**
     * @inheritDoc
     */
    public function getBoxItemRender(array $config = []): RenderInterface
    {
        $config = array_merge(['ratio' => '1:1'], $config);

        return $this->builder
            ->setView('Items.BoxItem')
            ->setConfig($config)
            ->build();
    }
}
