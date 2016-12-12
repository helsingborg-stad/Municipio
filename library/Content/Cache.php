<?php

namespace Municipio\Content;

class Cache
{
    public function __construct()
    {
        add_action('save_post', '\Municipio\Helper\Cache::clearCache', 10, 2);
    }
}
