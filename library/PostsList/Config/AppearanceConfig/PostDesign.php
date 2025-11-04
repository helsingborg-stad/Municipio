<?php

namespace Municipio\PostsList\Config\AppearanceConfig;

enum PostDesign: string
{
    case CARD       = 'card';
    case COMPRESSED = 'compressed';
    case COLLECTION = 'collection';
    case BLOCK      = 'block';
    case NEWSITEM   = 'newsitem';
    case SCHEMA     = 'schema';
    case TABLE      = 'table';
}
