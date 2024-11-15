<?php

namespace Municipio\PostObject\Renderer\PostObjectCollectionRenderer;

enum PostObjectCollectionRendererType: string
{
    case BlockItemCollection         = 'BlockItemCollection';
    case CardItemCollection          = 'CardItemCollection';
    case CollectionItemCollection    = 'CollectionItemCollection';
    case CompressedItemCollection    = 'CompressedItemCollection';
    case NewsItemCollection          = 'NewsItemCollection';
    case SchemaProjectItemCollection = 'SchemaProjectItemCollection';
    case SegmentGridItemCollection   = 'SegmentGridItemCollection';
}
