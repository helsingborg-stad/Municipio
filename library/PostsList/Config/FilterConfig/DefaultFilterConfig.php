<?php

namespace Municipio\PostsList\Config\FilterConfig;

/**
 * Default filter configuration implementation
 */
class DefaultFilterConfig implements FilterConfigInterface
{
    /**
     * @inheritDoc
     */
    public function isTextSearchEnabled(): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function isDateFilterEnabled(): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function getTaxonomiesEnabledForFiltering(): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function showReset(): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function getResetUrl(): null|string
    {
        return null;
    }
}
