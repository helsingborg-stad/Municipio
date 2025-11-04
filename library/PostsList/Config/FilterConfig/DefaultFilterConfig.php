<?php

namespace Municipio\PostsList\Config\FilterConfig;

class DefaultFilterConfig implements FilterConfigInterface
{
    public function isEnabled(): bool
    {
        return false;
    }

    public function isTextSearchEnabled(): bool
    {
        return false;
    }

    public function isDateFilterEnabled(): bool
    {
        return false;
    }

    public function getTaxonomiesEnabledForFiltering(): array
    {
        return [];
    }

    public function showReset(): bool
    {
        return false;
    }

    public function getResetUrl(): ?string
    {
        return null;
    }
}
