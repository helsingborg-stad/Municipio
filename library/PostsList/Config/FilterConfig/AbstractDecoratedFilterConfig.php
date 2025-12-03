<?php

namespace Municipio\PostsList\Config\FilterConfig;

/**
 * Abstract implementation of FilterConfigInterface used for decorating
 */
abstract class AbstractDecoratedFilterConfig implements FilterConfigInterface
{
    protected FilterConfigInterface $innerConfig;

    /**
     * @inheritDoc
     */
    public function isTextSearchEnabled(): bool
    {
        return $this->innerConfig->isTextSearchEnabled();
    }

    /**
     * @inheritDoc
     */
    public function isDateFilterEnabled(): bool
    {
        return $this->innerConfig->isDateFilterEnabled();
    }

    /**
     * @inheritDoc
     */
    public function getTaxonomiesEnabledForFiltering(): array
    {
        return $this->innerConfig->getTaxonomiesEnabledForFiltering();
    }

    /**
     * @inheritDoc
     */
    public function showReset(): bool
    {
        return $this->innerConfig->showReset();
    }

    /**
     * @inheritDoc
     */
    public function getResetUrl(): null|string
    {
        return $this->innerConfig->getResetUrl();
    }
}
