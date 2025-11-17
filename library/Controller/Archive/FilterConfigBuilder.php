<?php

namespace Municipio\Controller\Archive;

use Municipio\PostsList\Config\FilterConfig\DefaultFilterConfig;
use Municipio\PostsList\Config\FilterConfig\FilterConfigInterface;

class FilterConfigBuilder
{
    private bool $isEnabled              = false;
    private string $resetUrl             = '';
    private bool $isDateFilterEnabled    = false;
    private bool $isTextSearchEnabled    = false;
    private array $taxonomyFilterConfigs = [];

    public function setEnabled(bool $isEnabled): self
    {
        $this->isEnabled = $isEnabled;
        return $this;
    }

    public function setResetUrl(string $resetUrl): self
    {
        $this->resetUrl = $resetUrl;
        return $this;
    }

    public function setDateFilterEnabled(bool $isDateFilterEnabled): self
    {
        $this->isDateFilterEnabled = $isDateFilterEnabled;
        return $this;
    }

    public function setTextSearchEnabled(bool $isTextSearchEnabled): self
    {
        $this->isTextSearchEnabled = $isTextSearchEnabled;
        return $this;
    }

    public function setTaxonomyFilterConfigs(array $taxonomyFilterConfigs): self
    {
        $this->taxonomyFilterConfigs = $taxonomyFilterConfigs;
        return $this;
    }

    public function build(): FilterConfigInterface
    {
        return new class (
            $this->isEnabled,
            $this->resetUrl,
            $this->isDateFilterEnabled,
            $this->isTextSearchEnabled,
            $this->taxonomyFilterConfigs
        ) extends DefaultFilterConfig {
            public function __construct(
                private bool $isEnabled,
                private string $resetUrl,
                private bool $isDateFilterEnabled,
                private bool $isTextSearchEnabled,
                private array $taxonomyFilterConfigs
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
        };
    }
}
