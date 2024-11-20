<?php

namespace Municipio\PostObject\Renderer;

enum RenderCollectionType: string
{
    case BlockItemCollection         = 'BlockItemCollection';
    case BoxGridItemCollection       = 'BoxGridItemCollection';
    case ListItemCollection          = 'ListItemCollection';
    case CardItemCollection          = 'CardItemCollection';
    case CollectionItemCollection    = 'CollectionItemCollection';
    case CompressedItemCollection    = 'CompressedItemCollection';
    case NewsItemCollection          = 'NewsItemCollection';
    case SchemaProjectItemCollection = 'SchemaProjectItemCollection';
    case SegmentGridItemCollection   = 'SegmentGridItemCollection';
    case BoxSlider                   = 'BoxSlider';
}
