<?php

namespace Municipio\Admin\Roles;

use Municipio\HooksRegistrar\Hookable;
use WpService\WpService;

/**
 * Subscriber Role.
 */
class Subscriber implements Hookable
{
    private WpService $wpService;

    /**
     * Constructor.
     */
    public function __construct(WpService $wpService)
    {
        $this->wpService = $wpService;
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
        if (is_object($role)) {
            $role->add_cap('read_private_posts');
            $role->add_cap('read_private_pages');
        }
    }
}
