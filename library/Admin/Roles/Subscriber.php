<?php

namespace Municipio\Admin\Roles;

use Municipio\HooksRegistrar\Hookable;
use WP_Role;
use WpService\WpService;

/**
 * Subscriber Role.
 */
class Subscriber implements Hookable
{
    /**
     * Constructor.
     */
    public function __construct(
        private WpService $wpService,
    ) {}

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

        if (is_a($role, 'WP_Role') && !$role->has_cap('read_private_posts')) {
            $role->add_cap('read_private_posts');
        }

        if (is_a($role, 'WP_Role') && !$role->has_cap('read_private_pages')) {
            $role->add_cap('read_private_pages');
        }

        if (is_a($role, 'WP_Role') && !$role->has_cap('read_private_anys')) {
            $role->add_cap('read_private_anys');
        }
    }
}
