<?php

declare(strict_types=1);

namespace Modularity\HooksRegistrar;

interface Hookable
{
    /**
     * Add hooks to WordPress.
     */
    public function addHooks(): void;
}
