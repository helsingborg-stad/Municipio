<?php

namespace Municipio\Helper\SiteSwitcher;

use WpService\Contracts\SwitchToBlog;
use WpService\Contracts\RestoreCurrentBlog;
use WpService\Contracts\GetOption;
use WpService\Contracts\IsMultisite;
use AcfService\Contracts\GetField;

class SiteSwitcher implements SiteSwitcherInterface
{
    public function __construct(private SwitchToBlog&RestoreCurrentBlog&GetOption&IsMultisite $wpService, private GetField $acfService)
    {
    }

    /**
     * @inheritDoc
     */
    public function runInSite(int $siteId, callable $callable, mixed $callableContext = null): mixed
    {
        if (!$this->wpService->isMultisite()) {
            return $callable(...func_get_args());
        }

        $this->wpService->switchToBlog($siteId);

        try {
            return $callable(...func_get_args());
        } finally {
            $this->wpService->restoreCurrentBlog();
        }
    }

    /**
     * @inheritDoc
     */
    public function getOptionFromSite(int $siteId, string $optionName): mixed
    {
        return $this->runInSite($siteId, function ($optionName) {
            return $this->wpService->getOption($optionName);
        }, $optionName);
    }

    /**
     * @inheritDoc
     */
    public function getFieldFromSite(int $siteId, string $fieldSelector): mixed
    {
        return $this->runInSite($siteId, function ($fieldSelector) {
            return $this->acfService->getField($fieldSelector, 'option');
        }, $fieldSelector);
    }
}
