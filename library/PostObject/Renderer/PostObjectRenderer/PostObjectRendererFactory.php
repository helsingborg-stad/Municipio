<?php

namespace Municipio\PostObject\Renderer\PostObjectRenderer;

use Municipio\PostObject\Renderer\PostObjectRenderer\PostObjectRendererInterface;

/**
 * Factory for creating PostObjectRenderers.
 */
class PostObjectRendererFactory implements PostObjectRendererFactoryInterface
{
    /**
     * @inheritDoc
     */
    public function create(PostObjectRendererType $type): PostObjectRendererInterface
    {
        return match ($type) {
            PostObjectRendererType::BlockItem => new BlockItem(),
            PostObjectRendererType::CardItem => new CardItem(),
            PostObjectRendererType::CollectionItem => new CollectionItem(),
            PostObjectRendererType::CompressedItem => new CompressedItem(),
            PostObjectRendererType::NewsItem => new NewsItem(),
            PostObjectRendererType::SchemaProjectItem => new SchemaProjectItem(),
            PostObjectRendererType::SegmentItem => new SegmentItem(),
            PostObjectRendererType::SegmentGridItem => new SegmentGridItem(),
            PostObjectRendererType::SegmentSliderItem => new SegmentSliderItem(),
        };
    }
}
