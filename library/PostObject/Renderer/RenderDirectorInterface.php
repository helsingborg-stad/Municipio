<?php

namespace Municipio\PostObject\Renderer;

interface RenderDirectorInterface
{
    /**
     * Get BoxGridCollection render.
     *
     * @param array $config
     * @return RenderInterface
     */
    public function getBoxGridItemCollectionRender(array $config = []): RenderInterface;

    /**
     * Get BoxGridItem render.
     *
     * @param array $config
     * @return RenderInterface
     */
    public function getBoxGridItemRender(array $config = []): RenderInterface;

    /**
     * Get CardItemCollection render.
     *
     * @param array $config
     * @return RenderInterface
     */
    public function getCardItemCollectionRender(array $config = []): RenderInterface;

    /**
     * Get CardItem render.
     *
     * @param array $config
     * @return RenderInterface
     */
    public function getCardItemRender(array $config = []): RenderInterface;

    /**
     * Get CollectionItemCollection render.
     *
     * @param array $config
     * @return RenderInterface
     */
    public function getCollectionItemCollectionRender(array $config = []): RenderInterface;

    /**
     * Get CollectionItem render.
     *
     * @param array $config
     * @return RenderInterface
     */
    public function getCollectionItemRender(array $config = []): RenderInterface;

    /**
     * Get CompressedItemCollection render.
     *
     * @param array $config
     * @return RenderInterface
     */
    public function getCompressedItemCollectionRender(array $config = []): RenderInterface;

    /**
     * Get CompressedItem render.
     *
     * @param array $config
     * @return RenderInterface
     */
    public function getCompressedItemRender(array $config = []): RenderInterface;

    /**
     * Get BlockItemCollection render.
     *
     * @param array $config
     * @return RenderInterface
     */
    public function getBlockItemCollectionRender(array $config = []): RenderInterface;

    /**
     * Get BlockItem render.
     *
     * @param array $config
     * @return RenderInterface
     */
    public function getBlockItemRender(array $config = []): RenderInterface;

    /**
     * Get ListItemCollection render.
     *
     * @param array $config
     * @return RenderInterface
     */
    public function getListItemCollectionRender(array $config = []): RenderInterface;

    /**
     * Get ListItem render.
     *
     * @param array $config
     * @return RenderInterface
     */
    public function getListItemRender(array $config = []): RenderInterface;

    /**
     * Get NewsItemCollection render.
     *
     * @param array $config
     * @return RenderInterface
     */
    public function getNewsItemCollectionRender(array $config = []): RenderInterface;

    /**
     * Get NewsItem render.
     *
     * @param array $config
     * @return RenderInterface
     */
    public function getNewsItemRender(array $config = []): RenderInterface;

    /**
     * Get SchemaProjectItemCollection render.
     *
     * @param array $config
     * @return RenderInterface
     */
    public function getSchemaProjectItemCollectionRender(array $config = []): RenderInterface;

    /**
     * Get SchemaProjectItem render.
     *
     * @param array $config
     * @return RenderInterface
     */
    public function getSchemaProjectItemRender(array $config = []): RenderInterface;

    /**
     * Get SegmentGridItemCollection render.
     *
     * @param array $config
     * @return RenderInterface
     */
    public function getSegmentGridItemCollectionRender(array $config = []): RenderInterface;

    /**
     * Get SegmentGridItem render.
     *
     * @param array $config
     * @return RenderInterface
     */
    public function getSegmentGridItemRender(array $config = []): RenderInterface;

    /**
     * Get SegmentSliderItem render.
     *
     * @param array $config
     * @return RenderInterface
     */
    public function getSegmentSliderItemRender(array $config = []): RenderInterface;

    /**
     * Get SegmentItemRender render.
     *
     * @param array $config
     * @return RenderInterface
     */
    public function getSegmentItemRender(array $config = []): RenderInterface;

    /**
     * Get BoxSliderItem render.
     *
     * @param array $config
     * @return RenderInterface
     */
    public function getBoxSliderItemRender(array $config = []): RenderInterface;

    /**
     * Get BoxItem render.
     *
     * @param array $config
     * @return RenderInterface
     */
    public function getBoxItemRender(array $config = []): RenderInterface;
}
