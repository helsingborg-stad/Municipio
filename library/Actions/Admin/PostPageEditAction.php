<?php

namespace Municipio\Actions\Admin;

use Municipio\HooksRegistrar\Hookable;
use WP_Screen;
use WpService\Contracts\AddAction;
use WpService\Contracts\DoAction;

/**
 * Class PostPageEditAction
 * This class is responsible for handling the action when a post page is edited in the admin area.
 * It hooks into the `current_screen` action and checks if the current screen is a post edit screen.
 * If so, it triggers a custom action with the post ID and post type.
 */
class PostPageEditAction implements Hookable
{
    public const ACTION = 'Municipio\post\page\edit';

    public function __construct(private AddAction&DoAction $wpService)
    {
    }

    public function addHooks(): void
    {
        $this->wpService->addAction('current_screen', [$this, 'doAction'], 10, 1);
    }

    public function doAction(WP_Screen $screen): void
    {
        if ($screen->base !== 'post' || empty($screen->post_type)) {
            return;
        }

        if (!isset($_GET['post']) || !is_numeric($_GET['post'])) {
            return;
        }

        $this->wpService->doAction(self::ACTION, (int) $_GET['post'], $screen->post_type);
    }
}
