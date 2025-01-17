<?php

namespace Municipio\Helper\SiteSwitcher;

use Municipio\Helper\WpService;
use WpService\Contracts\RestoreCurrentBlog;
use WpService\Contracts\SwitchToBlog;

class SiteSwitcher
{
    public function __construct(private SwitchToBlog&RestoreCurrentBlog $wpService)
    {
    }

    /**
     * Execute a callable within the context of a specific site.
     *
     * @param int $siteId
     * @param callable $callable
     * @param mixed $callableContext Contextual data to pass to the callable.
     * @return mixed The result of the callable execution.
     */
    public function runInSite(int $siteId, callable $callable, mixed $callableContext = null): mixed
    {
        $this->wpService->switchToBlog($siteId);

        try {
            return $callable(...func_get_args());
        } finally {
            $this->wpService->restoreCurrentBlog();
        }
    }
}
