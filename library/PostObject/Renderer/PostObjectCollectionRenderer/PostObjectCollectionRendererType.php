<?php

namespace Municipio\PostObject\Renderer\PostObjectCollectionRenderer;

enum PostObjectCollectionRendererType: string
{
    case BlockItemCollection         = 'BlockItemCollection';
    case BoxItemCollection           = 'BoxItemCollection';
    case BoxGridItemCollection       = 'BoxGridItemCollection';
    case CardItemCollection          = 'CardItemCollection';
    case CollectionItemCollection    = 'CollectionItemCollection';
    case CompressedItemCollection    = 'CompressedItemCollection';
    case ListItemCollection          = 'ListItemCollection';
    case NewsItemCollection          = 'NewsItemCollection';
    case SchemaProjectItemCollection = 'SchemaProjectItemCollection';
    case SegmentGridItemCollection   = 'SegmentGridItemCollection';
    case SegmentItemSlider           = 'SegmentItemSlider';
}
