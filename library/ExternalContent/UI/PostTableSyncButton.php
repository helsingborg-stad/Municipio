<?php

namespace Municipio\ExternalContent\UI;

use Municipio\ExternalContent\Rest\AjaxSync;
use Municipio\HooksRegistrar\Hookable;
use WpService\Contracts\AddAction;
use WpService\Contracts\AdminUrl;
use WpService\Contracts\CurrentUserCan;
use WpService\Contracts\EscHtml;
use WpService\Contracts\GetCurrentScreen;
use WpService\Contracts\SubmitButton;
use WpService\Contracts\WpNonceUrl;

/**
 * Class PostTableSyncButton
 *
 * This class adds a sync button to the post table in the WordPress admin.
 */
class PostTableSyncButton implements Hookable
{
    /**
     * Constructor.
     */
    public function __construct(
        private array $sourceConfigs,
        private AddAction&GetCurrentScreen&SubmitButton&WpNonceUrl&EscHtml&CurrentUserCan&AdminUrl $wpService
    ) {
    }

    /**
     * @inheritDoc
     */
    public function addHooks(): void
    {
        if (!$this->wpService->currentUserCan('activate_plugins', null)) {
            return;
        }

        $this->wpService->addAction('manage_posts_extra_tablenav', array($this, 'addSyncButton'));
    }

    /**
     * Add sync button to the post table.
     *
     * @param string $which
     */
    public function addSyncButton(string $which)
    {
        $postTypeHasExternalContentSource = array_filter(
            $this->sourceConfigs,
            fn($config) => $config->getPostType() === $this->wpService->getCurrentScreen()->post_type
        );

        if (empty($postTypeHasExternalContentSource)) {
            return;
        }

        $classes  = 'button button-primary';
        $label    = __('Sync all posts from remote source', 'municipio');
        $ajaxUrl  = $this->wpService->adminUrl('admin-ajax.php');
        $ajaxUrl .= '?action=' . AjaxSync::$action . '&post_type=' . $this->wpService->getCurrentScreen()->post_type;

        echo '<a data-js-progress-url="' . esc_url($ajaxUrl) . '" class="' . $classes . '" href="#">' . $label . '</a>';
    }
}
