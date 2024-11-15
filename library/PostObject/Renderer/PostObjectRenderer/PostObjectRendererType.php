<?php

namespace Municipio\PostObject\Renderer\PostObjectRenderer;

enum PostObjectRendererType: string
{
    case BlockItem         = 'BlockItem';
    case BoxItem           = 'BoxItem';
    case BoxGridItem       = 'BoxGridItem';
    case BoxSliderItem     = 'BoxSliderItem';
    case CardItem          = 'CardItem';
    case CollectionItem    = 'CollectionItem';
    case CompressedItem    = 'CompressedItem';
    case ListItem          = 'ListItem';
    case NewsItem          = 'NewsItem';
    case SchemaProjectItem = 'SchemaProjectItem';
    case SegmentItem       = 'SegmentItem';
    case SegmentGridItem   = 'SegmentGridItem';
    case SegmentSliderItem = 'SegmentSliderItem';
}
