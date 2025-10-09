<?php

namespace Municipio\SchemaData\ExternalContent\UI;

use Municipio\HooksRegistrar\Hookable;
use WP_Post;
use WpService\Contracts\AddFilter;
use WpService\Contracts\CurrentUserCan;
use WpService\Contracts\WpNonceUrl;

/**
 * Class PageRowActionsSyncButton
 *
 * This class adds a sync button to the page and post row actions in the WordPress admin.
 */
class PageRowActionsSyncButton implements Hookable
{
    /**
     * Constructor.
     */
    public function __construct(private array $sourceConfigs, private AddFilter&WpNonceUrl&CurrentUserCan $wpService)
    {
    }

    /**
     * @inheritDoc
     */
    public function addHooks(): void
    {
        if (!$this->wpService->currentUserCan('administrator', null)) {
            return;
        }

        $this->wpService->addFilter('page_row_actions', array($this, 'addSyncButton'), 10, 2);
        $this->wpService->addFilter('post_row_actions', array($this, 'addSyncButton'), 10, 2);
    }

    /**
     * Adds a sync button to the row actions for pages and posts.
     *
     * @param array $actions The existing row actions.
     * @param WP_Post $post The current post object.
     * @return array The modified row actions.
     */
    public function addSyncButton(array $actions, WP_Post $post): array
    {
        $postTypeHasExternalContentSource = array_filter(
            $this->sourceConfigs,
            fn($config) => $config->getPostType() === $post->post_type
        );

        if (empty($postTypeHasExternalContentSource)) {
            return $actions;
        }

        $urlParams = sprintf(
            '&%s&%s=%s',
            \Municipio\SchemaData\ExternalContent\SyncHandler\Triggers\TriggerSyncFromGetParams::GET_PARAM_TRIGGER,
            \Municipio\SchemaData\ExternalContent\SyncHandler\Triggers\TriggerSyncFromGetParams::GET_PARAM_POST_ID,
            $post->ID
        );

        $url = $this->wpService->wpNonceUrl($_SERVER['REQUEST_URI'] ?? '', -1) . $urlParams;

        $actions[\Municipio\SchemaData\ExternalContent\SyncHandler\Triggers\TriggerSyncFromGetParams::GET_PARAM_TRIGGER] = sprintf(
            '<a href="%s">%s</a>',
            $url,
            __('Sync this post from remote source', 'municipio')
        );

        return $actions;
    }
}
