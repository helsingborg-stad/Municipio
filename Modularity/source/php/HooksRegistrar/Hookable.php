<?php

namespace Modularity\HooksRegistrar;

interface Hookable
{
    /**
     * Add hooks to WordPress.
     */
    public function addHooks(): void;
}
