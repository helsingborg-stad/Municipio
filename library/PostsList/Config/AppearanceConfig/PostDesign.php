<?php

namespace Municipio\PostsList\Config\AppearanceConfig;

enum PostDesign: string
{
    case CARD       = 'card';
    case COMPRESSED = 'compressed';
    case COLLECTION = 'collection';
    case NEWSITEM   = 'newsitem';
}
