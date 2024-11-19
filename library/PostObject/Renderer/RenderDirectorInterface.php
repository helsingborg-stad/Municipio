<?php

namespace Municipio\PostObject\Renderer;

use Municipio\PostObject\PostObjectInterface;

interface RenderDirectorInterface
{
    /**
     * Get BoxGridCollection render.
     *
     * @param PostObjectInterface[] $postObjects
     * @param array $config
     * @return RenderInterface
     */
    public function getBoxGridItemCollectionRender(array $postObjects, array $config = []): RenderInterface;

    /**
     * Get BoxGridItem render.
     *
     * @param PostObjectInterface $postObject
     * @param array $config
     * @return RenderInterface
     */
    public function getBoxGridItemRender(PostObjectInterface $postObject, array $config = []): RenderInterface;

    /**
     * Get CardItemCollection render.
     *
     * @param PostObjectInterface[] $postObjects
     * @param array $config
     * @return RenderInterface
     */
    public function getCardItemCollectionRender(array $postObjects, array $config = []): RenderInterface;

    /**
     * Get CardItem render.
     *
     * @param PostObjectInterface $postObject
     * @param array $config
     * @return RenderInterface
     */
    public function getCardItemRender(PostObjectInterface $postObject, array $config = []): RenderInterface;

    /**
     * Get CollectionItemCollection render.
     *
     * @param PostObjectInterface[] $postObjects
     * @param array $config
     * @return RenderInterface
     */
    public function getCollectionItemCollectionRender(array $postObjects, array $config = []): RenderInterface;

    /**
     * Get CollectionItem render.
     *
     * @param PostObjectInterface $postObject
     * @param array $config
     * @return RenderInterface
     */
    public function getCollectionItemRender(PostObjectInterface $postObject, array $config = []): RenderInterface;

    /**
     * Get CompressedItemCollection render.
     *
     * @param PostObjectInterface[] $postObjects
     * @param array $config
     * @return RenderInterface
     */
    public function getCompressedItemCollectionRender(array $postObjects, array $config = []): RenderInterface;

    /**
     * Get CompressedItem render.
     *
     * @param PostObjectInterface $postObject
     * @param array $config
     * @return RenderInterface
     */
    public function getCompressedItemRender(PostObjectInterface $postObject, array $config = []): RenderInterface;

    /**
     * Get BlockItemCollection render.
     *
     * @param PostObjectInterface[] $postObjects
     * @param array $config
     * @return RenderInterface
     */
    public function getBlockItemCollectionRender(array $postObjects, array $config = []): RenderInterface;

    /**
     * Get BlockItem render.
     *
     * @param PostObjectInterface $postObject
     * @param array $config
     * @return RenderInterface
     */
    public function getBlockItemRender(PostObjectInterface $postObject, array $config = []): RenderInterface;

    /**
     * Get ListItemCollection render.
     *
     * @param PostObjectInterface[] $postObjects
     * @param array $config
     * @return RenderInterface
     */
    public function getListItemCollectionRender(array $postObjects, array $config = []): RenderInterface;

    /**
     * Get ListItem render.
     *
     * @param PostObjectInterface $postObject
     * @param array $config
     * @return RenderInterface
     */
    public function getListItemRender(PostObjectInterface $postObject, array $config = []): RenderInterface;

    /**
     * Get NewsItemCollection render.
     *
     * @param PostObjectInterface[] $postObjects
     * @param array $config
     * @return RenderInterface
     */
    public function getNewsItemCollectionRender(array $postObjects, array $config = []): RenderInterface;

    /**
     * Get NewsItem render.
     *
     * @param PostObjectInterface $postObject
     * @param array $config
     * @return RenderInterface
     */
    public function getNewsItemRender(PostObjectInterface $postObject, array $config = []): RenderInterface;

    /**
     * Get SchemaProjectItemCollection render.
     *
     * @param PostObjectInterface[] $postObjects
     * @param array $config
     * @return RenderInterface
     */
    public function getSchemaProjectItemCollectionRender(array $postObjects, array $config = []): RenderInterface;

    /**
     * Get SchemaProjectItem render.
     *
     * @param PostObjectInterface $postObject
     * @param array $config
     * @return RenderInterface
     */
    public function getSchemaProjectItemRender(PostObjectInterface $postObject, array $config = []): RenderInterface;

    /**
     * Get SegmentGridItemCollection render.
     *
     * @param PostObjectInterface[] $postObjects
     * @param array $config
     * @return RenderInterface
     */
    public function getSegmentGridItemCollectionRender(array $postObjects, array $config = []): RenderInterface;

    /**
     * Get SegmentGridItem render.
     *
     * @param PostObjectInterface $postObject
     * @param array $config
     * @return RenderInterface
     */
    public function getSegmentGridItemRender(PostObjectInterface $postObject, array $config = []): RenderInterface;

    /**
     * Get SegmentSliderItem render.
     *
     * @param PostObjectInterface $postObject
     * @param array $config
     * @return RenderInterface
     */
    public function getSegmentSliderItemRender(PostObjectInterface $postObject, array $config = []): RenderInterface;

    /**
     * Get SegmentItemRender render.
     *
     * @param PostObjectInterface $postObject
     * @param array $config
     * @return RenderInterface
     */
    public function getSegmentItemRender(PostObjectInterface $postObject, array $config = []): RenderInterface;

    /**
     * Get BoxSliderItem render.
     *
     * @param PostObjectInterface $postObject
     * @param array $config
     * @return RenderInterface
     */
    public function getBoxSliderItemRender(PostObjectInterface $postObject, array $config = []): RenderInterface;

    /**
     * Get BoxItem render.
     *
     * @param PostObjectInterface $postObject
     * @param array $config
     * @return RenderInterface
     */
    public function getBoxItemRender(PostObjectInterface $postObject, array $config = []): RenderInterface;
}
