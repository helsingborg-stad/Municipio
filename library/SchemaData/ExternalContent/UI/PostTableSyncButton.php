<?php

declare(strict_types=1);

namespace Municipio\SchemaData\ExternalContent\UI;

use Municipio\ProgressReporter\UI\AdminProgressActionButton;
use Municipio\ProgressReporter\UI\AdminProgressActionButtonConfig;
use Municipio\SchemaData\ExternalContent\Rest\AjaxSync;
use Municipio\HooksRegistrar\Hookable;
use WpService\Contracts\AddAction;
use WpService\Contracts\CurrentUserCan;
use WpService\Contracts\GetCurrentScreen;
use WpService\Contracts\__;

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
        private AddAction&CurrentUserCan&GetCurrentScreen&__ $wpService,
        private AdminProgressActionButton $progressActionButton,
    ) {
    }

    /**
     * @inheritDoc
     */
    public function addHooks(): void
    {
        if (!$this->wpService->currentUserCan('administrator', null)) {
            return;
        }

        $this->wpService->addAction('manage_posts_extra_tablenav', [$this, 'addSyncButton']);
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

        if ($postTypeHasExternalContentSource === []) {
            return;
        }

        echo $this->progressActionButton->renderGet(new AdminProgressActionButtonConfig(
            action: AjaxSync::$action,
            label: $this->wpService->__('Sync all posts from remote source', 'municipio'),
            parameters: ['post_type' => $this->wpService->getCurrentScreen()->post_type],
        ));
    }
}
