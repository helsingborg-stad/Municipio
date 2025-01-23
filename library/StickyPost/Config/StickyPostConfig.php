<?php

namespace Municipio\StickyPost\Config;

class StickyPostConfig implements StickyPostConfigInterface
{
    public function getStickyPostMetaKey(): string
    {
        return 'sticky-post';
    }
}