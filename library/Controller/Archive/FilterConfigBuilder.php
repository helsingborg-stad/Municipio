<?php

namespace Municipio\Controller\Archive;

use Municipio\PostsList\Config\FilterConfig\DefaultFilterConfig;
use Municipio\PostsList\Config\FilterConfig\FilterConfigInterface;

/**
 * Builder for FilterConfig
 */
class FilterConfigBuilder
{
    private bool $isEnabled              = false;
    private string $resetUrl             = '';
    private bool $isDateFilterEnabled    = false;
    private bool $isTextSearchEnabled    = false;
    private array $taxonomyFilterConfigs = [];
    private bool $showReset              = false;

    /**
     * Set is enabled
     */
    public function setEnabled(bool $isEnabled): self
    {
        $this->isEnabled = $isEnabled;
        return $this;
    }

    /**
     * Set reset URL
     */
    public function setResetUrl(string $resetUrl): self
    {
        $this->resetUrl = $resetUrl;
        return $this;
    }

    /**
     * Set is date filter enabled
     */
    public function setDateFilterEnabled(bool $isDateFilterEnabled): self
    {
        $this->isDateFilterEnabled = $isDateFilterEnabled;
        return $this;
    }

    /**
     * Set is text search enabled
     */
    public function setTextSearchEnabled(bool $isTextSearchEnabled): self
    {
        $this->isTextSearchEnabled = $isTextSearchEnabled;
        return $this;
    }

    /**
     * Set taxonomy filter configs
     */
    public function setTaxonomyFilterConfigs(array $taxonomyFilterConfigs): self
    {
        $this->taxonomyFilterConfigs = $taxonomyFilterConfigs;
        return $this;
    }

    /**
     * Set show reset
     * @param bool $showReset
     */
    public function setShowReset(bool $showReset): self
    {
        $this->showReset = $showReset;
        return $this;
    }

    /**
     * Build FilterConfig
     */
    public function build(): FilterConfigInterface
    {
        return new class (
            $this->isEnabled,
            $this->resetUrl,
            $this->isDateFilterEnabled,
            $this->isTextSearchEnabled,
            $this->taxonomyFilterConfigs,
            $this->showReset
        ) extends DefaultFilterConfig {
            /**
             * Constructor
             */
            public function __construct(
                private bool $isEnabled,
                private string $resetUrl,
                private bool $isDateFilterEnabled,
                private bool $isTextSearchEnabled,
                private array $taxonomyFilterConfigs,
                private bool $showReset
            ) {
            }

            /**
             * @inheritDoc
             */
            public function isEnabled(): bool
            {
                return $this->isEnabled;
            }

            /**
             * @inheritDoc
             */
            public function isTextSearchEnabled(): bool
            {
                return $this->isTextSearchEnabled;
            }

            /**
             * @inheritDoc
             */
            public function getResetUrl(): ?string
            {
                return $this->resetUrl;
            }

            /**
             * @inheritDoc
             */
            public function isDateFilterEnabled(): bool
            {
                return $this->isDateFilterEnabled;
            }

            /**
             * @inheritDoc
             */
            public function getTaxonomiesEnabledForFiltering(): array
            {
                return $this->taxonomyFilterConfigs;
            }

            /**
             * @inheritDoc
             */
            public function showReset(): bool
            {
                return $this->showReset;
            }
        };
    }
}
