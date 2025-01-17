<?php

namespace Municipio\Helper\SiteSwitcher;

class SiteSwitcher
{
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
        switch_to_blog($siteId);

        try {
            return $callable(...func_get_args());
        } finally {
            restore_current_blog();
        }
    }
}