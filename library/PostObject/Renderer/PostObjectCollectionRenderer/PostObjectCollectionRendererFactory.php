<?php

namespace Municipio\PostObject\Renderer\PostObjectCollectionRenderer;

/**
 * Factory for creating PostObjectCollectionRenderer.
 */
class PostObjectCollectionRendererFactory implements PostObjectCollectionRendererFactoryInterface
{
    /**
     * @inheritDoc
     */
    public function create(PostObjectCollectionRendererType $type): PostObjectCollectionRendererInterface
    {
        return match ($type) {
            PostObjectCollectionRendererType::BlockItemCollection => new BlockItemCollection(),
            PostObjectCollectionRendererType::BoxItemCollection => new BoxItemCollection(),
            PostObjectCollectionRendererType::BoxGridItemCollection => new BoxGridItemCollection(),
            PostObjectCollectionRendererType::CardItemCollection => new CardItemCollection(),
            PostObjectCollectionRendererType::CollectionItemCollection => new CollectionItemCollection(),
            PostObjectCollectionRendererType::CompressedItemCollection => new CompressedItemCollection(),
            PostObjectCollectionRendererType::ListItemCollection => new ListItemCollection(),
            PostObjectCollectionRendererType::NewsItemCollection => new NewsItemCollection(),
            PostObjectCollectionRendererType::SchemaProjectItemCollection => new SchemaProjectItemCollection(),
            PostObjectCollectionRendererType::SegmentGridItemCollection => new SegmentGridItemCollection(),
            PostObjectCollectionRendererType::SegmentItemSlider => new SegmentItemSlider(),
        };
    }
}
