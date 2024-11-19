<?php

namespace Municipio\PostObject\Renderer;

enum RenderItemType: string
{
    case BlockItem         = 'BlockItem';
    case BoxGridItem       = 'BoxGridItem';
    case BoxItem           = 'BoxItem';
    case BoxSliderItem     = 'BoxSliderItem';
    case CardItem          = 'CardItem';
    case CollectionItem    = 'CollectionItem';
    case CompressedItem    = 'CompressedItem';
    case ListItem          = 'ListItem';
    case NewsItem          = 'NewsItem';
    case SchemaProjectItem = 'SchemaProjectItem';
    case SegmentGridItem   = 'SegmentGridItem';
    case SegmentItem       = 'SegmentItem';
    case SegmentSliderItem = 'SegmentSliderItem';
}
