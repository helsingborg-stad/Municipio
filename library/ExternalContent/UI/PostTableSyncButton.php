<?php

namespace Municipio\ExternalContent\UI;

use Municipio\HooksRegistrar\Hookable;
use WpService\Contracts\AddAction;
use WpService\Contracts\GetCurrentScreen;
use WpService\Contracts\SubmitButton;

class PostTableSyncButton implements Hookable
{
/**
     * PageRowActionsSyncButton constructor.
     *
     * @param \Municipio\ExternalContent\Config\SourceConfig[] $sourceConfigs
     * @param AddAction&GetCurrentScreen&SubmitButton $wpService
     */
    public function __construct(
        private array $sourceConfigs,
        private AddAction&GetCurrentScreen&SubmitButton $wpService
    ) {
    }

    public function addHooks(): void
    {
        $this->wpService->addAction('manage_posts_extra_tablenav', array($this, 'addSyncButton'));
    }

    public function addSyncButton(string $which)
    {
        $postTypeHasExternalContentSource = array_filter($this->sourceConfigs, fn($config) => $config->getPostType() === $this->wpService->getCurrentScreen()->post_type);

        if (empty($postTypeHasExternalContentSource)) {
            return;
        }

            $this->wpService->submitButton(
                __('Sync all posts from remote source'),
                'primary',
                \Municipio\ExternalContent\Sync\Triggers\TriggerSyncFromGetParams::GET_PARAM_TRIGGER,
                false
            );
    }
}
