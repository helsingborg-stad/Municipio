<?php

namespace Municipio\PostObject\Renderer\PostObjectRenderer;

enum PostObjectRendererType: string
{
    case BlockItem         = 'BlockItem';
    case CardItem          = 'CardItem';
    case CollectionItem    = 'CollectionItem';
    case CompressedItem    = 'CompressedItem';
    case NewsItem          = 'NewsItem';
    case SchemaProjectItem = 'SchemaProjectItem';
    case SegmentItem       = 'SegmentItem';
    case SegmentGridItem   = 'SegmentGridItem';
    case SegmentSliderItem = 'SegmentSliderItem';
}
