<?php

namespace Municipio\ExternalContent\Rest;

use Municipio\ExternalContent\Config\SourceConfigInterface;
use Municipio\ExternalContent\SyncHandler\SyncHandler;
use Municipio\ExternalContent\SyncHandler\SyncInProgress\PostTypeSyncInProgressInterface;
use Municipio\Helper\WpService;
use Municipio\HooksRegistrar\Hookable;
use Municipio\ProgressReporter\HttpHeader\HttpHeader;
use Municipio\ProgressReporter\OutputBuffer\OutputBuffer;
use WpService\Contracts\__;

/**
 * Class AjaxSync
 */
class AjaxSync implements Hookable
{
    public static string $action = 'municipio_external_content_sync';

    /**
     * Constructor
     *
     * @param SourceConfigInterface[] $sourceConfigs
     */
    public function __construct(private array $sourceConfigs, private PostTypeSyncInProgressInterface $inProgress, private __ $wpService)
    {
    }

    /**
     * @inheritDoc
     */
    public function addHooks(): void
    {
        add_action('wp_ajax_' . self::$action, [$this, 'handleRequest']);
    }

    /**
     * Handles the AJAX request for syncing external content.
     *
     * @return void
     * @throws \InvalidArgumentException if the post_type parameter is missing.
     */
    public function handleRequest(): void
    {
        $postType = $_REQUEST['post_type'] ?? null;
        $postId   = $_REQUEST['post_id'] ?? null;

        if (empty($postType)) {
            // phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped
            throw new \InvalidArgumentException($this->wpService->__('Missing post_type parameter', 'municipio'));
        }

        require_once(ABSPATH . 'wp-admin/includes/media.php');
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/image.php');

        $progressReporter = new \Municipio\ProgressReporter\SseProgressReporterService(new HttpHeader(), new OutputBuffer());
        $syncHandler      = new SyncHandler($this->sourceConfigs, WpService::get(), $progressReporter);
        $progressReporter->start();

        if ($this->inProgress->isInProgress($postType)) {
            $progressReporter->finish($this->wpService->__('Sync already in progress', 'municipio'));
            return;
        }

        $this->inProgress->setInProgress($postType, true);

        $syncHandler->sync($postType, $postId);

        $this->inProgress->setInProgress($postType, false);

        $progressReporter->finish($this->wpService->__('Sync completed. Reload page to see changes.', 'municipio'));
    }
}
