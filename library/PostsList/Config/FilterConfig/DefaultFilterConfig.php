<?php

namespace Municipio\PostsList\Config\FilterConfig;

class DefaultFilterConfig implements FilterConfigInterface
{
    public function isEnabled(): bool
    {
        return false;
    }
}
