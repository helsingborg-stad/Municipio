<?php

namespace Municipio\Admin\Roles;

use WP_Role;
use Municipio\HooksRegistrar\Hookable;
use WpService\WpService;

/**
 * Subscriber Role.
 */
class Subscriber implements Hookable
{
    /**
     * Constructor.
     */
    public function __construct(private WpService $wpService)
    {
    }


    /**
     * @inheritDoc
     */
    public function addHooks(): void
    {
        $this->wpService->addAction('init', [$this, 'makePrivateReadable'], 5);
    }


    /**
     * @inheritDoc
     */
    public function makePrivateReadable()
    {
        $role = $this->wpService->getRole('subscriber');

        if (is_a($role, 'WP_Role') && empty($role->capabilities['read_private_posts'])) {
            $role->add_cap('read_private_posts');
        }

        if (is_a($role, 'WP_Role') && empty($role->capabilities['read_private_pages'])) {
            $role->add_cap('read_private_pages');
        }
    }
}
