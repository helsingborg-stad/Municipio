<?php

namespace Municipio\Helper\SiteSwitcher\Contracts;

interface RunInSite
{
    /**
     * Execute a callable within the context of a specific site.
     *
     * @param int $siteId
     * @param callable $callable
     * @param mixed $callableContext Contextual data to pass to the callable.
     * @return mixed The result of the callable execution.
     */
    public function runInSite(int $siteId, callable $callable, mixed $callableContext = null): mixed;
}
