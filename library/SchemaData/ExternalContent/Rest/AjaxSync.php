<?php

declare(strict_types=1);


namespace Municipio\SchemaData\ExternalContent\Rest;

use Municipio\ProgressReporter\AjaxAction\AbstractProgressAjaxAction;
use Municipio\ProgressReporter\AjaxAction\ProgressAjaxActionConfig;
use Municipio\ProgressReporter\AjaxAction\ProgressAjaxActionMessages;
use Municipio\SchemaData\ExternalContent\Config\SourceConfigInterface;
use Municipio\SchemaData\ExternalContent\SyncHandler\SyncInProgress\PostTypeSyncInProgressInterface;
use Municipio\ProgressReporter\ProgressReporterInterface;
use Municipio\SchemaData\ExternalContent\SyncHandler\SyncHandlerInterface;
use WpService\Contracts\AddAction;
use WpService\Contracts\CheckAjaxReferer;
use WpService\Contracts\CurrentUserCan;
use WpService\Contracts\__;

/**
 * Class AjaxSync
 */
class AjaxSync extends AbstractProgressAjaxAction
{
    public static string $action = 'municipio_external_content_sync';

    /**
     * Constructor
     *
     * @param SourceConfigInterface[] $sourceConfigs
     */
    public function __construct(
        private array $sourceConfigs,
        private PostTypeSyncInProgressInterface $inProgress,
        ProgressReporterInterface $progressReporter,
        private SyncHandlerInterface $syncHandler,
        private AddAction&CheckAjaxReferer&CurrentUserCan&__ $translationService,
    ) {
        parent::__construct(
            $translationService,
            $progressReporter,
            new ProgressAjaxActionConfig(
                action: self::$action,
                requiredCapability: 'administrator',
                messages: new ProgressAjaxActionMessages(
                    unauthorized: $translationService->__('You are not allowed to sync external content.', 'municipio'),
                    invalidNonce: $translationService->__('The sync request could not be verified. Reload the page and try again.', 'municipio'),
                ),
                nonceAction: self::$action,
            ),
        );
    }

    /**
     * Handles the AJAX request for syncing external content.
     *
     * @return void
     * @throws \InvalidArgumentException if the post_type parameter is missing.
     */
    protected function execute(): string
    {
        $postType = $_GET['post_type'] ?? null;
        $postId   = $_GET['post_id'] ?? null;

        if (!is_string($postType) || $postType === '') {
            // phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped
            throw new \InvalidArgumentException($this->translationService->__('Missing post_type parameter', 'municipio'));
        }

        require_once(ABSPATH . 'wp-admin/includes/media.php');
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/image.php');

        if ($this->inProgress->isInProgress($postType)) {
            return $this->translationService->__('Sync already in progress', 'municipio');
        }

        $this->inProgress->setInProgress($postType, true);

        try {
            $this->syncHandler->sync($postType, is_numeric($postId) ? (int) $postId : null);
        } finally {
            $this->inProgress->setInProgress($postType, false);
        }

        return $this->translationService->__('Sync completed. Reload page to see changes.', 'municipio');
    }
}
