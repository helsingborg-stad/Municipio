<?php

namespace Municipio\PostObject\Renderer;

enum RenderType: string
{
    // Item types
    case BlockItem         = 'BlockItem';
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

    // Collection types
    case BlockItemCollection         = 'BlockItemCollection';
    case BoxGridItemCollection       = 'BoxGridItemCollection';
    case CardItemCollection          = 'CardItemCollection';
    case CollectionItemCollection    = 'CollectionItemCollection';
    case CompressedItemCollection    = 'CompressedItemCollection';
    case ListItemCollection          = 'ListItemCollection';
    case NewsItemCollection          = 'NewsItemCollection';
    case SchemaProjectItemCollection = 'SchemaProjectItemCollection';
    case SegmentGridItemCollection   = 'SegmentGridItemCollection';
}
