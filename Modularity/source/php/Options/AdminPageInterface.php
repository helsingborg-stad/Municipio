<?php

namespace Modularity\Options;

/**
 * Interface for creating admin pages in WordPress.
 */
interface AdminPageInterface
{
    /**
     * Add hooks for the admin page.
     *
     * @return void
     */
    public function addHooks(): void;

    /**
     * Add the admin page to WordPress.
     *
     * @return void
     */
    public function addAdminPage(): void;
}
