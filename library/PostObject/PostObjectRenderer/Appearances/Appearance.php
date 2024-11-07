<?php

namespace Municipio\PostObject\PostObjectRenderer\Appearances;

enum Appearance: string
{
    case BlockItem         = 'BlockItem';
    case CardItem          = 'CardItem';
    case CollectionItem    = 'CollectionItem';
    case CompressedItem    = 'CompressedItem';
    case ListItem          = 'ListItem';
    case SchemaProjectItem = 'SchemaProjectItem';
}
