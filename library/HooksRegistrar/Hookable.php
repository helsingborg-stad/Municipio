<?php

namespace Municipio\HooksRegistrar;

interface Hookable
{
    /**
     * Add hooks to WordPress.
     */
    public function addHooks(): void;
}
