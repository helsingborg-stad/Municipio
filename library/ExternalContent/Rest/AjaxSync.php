<?php

namespace Municipio\ExternalContent\Rest;

use Municipio\ExternalContent\Config\SourceConfigInterface;
use Municipio\ExternalContent\SyncHandler\SyncHandler;
use Municipio\ExternalContent\SyncHandler\SyncInProgress\PostTypeSyncInProgressInterface;
use Municipio\Helper\WpService;
use Municipio\HooksRegistrar\Hookable;
use Municipio\ProgressReporter\HttpHeader\HttpHeader;

class AjaxSync implements Hookable
{
    public static string $action = 'municipio_external_content_sync';

    /**
     * Constructor
     *
     * @param SourceConfigInterface[] $sourceConfigs
     */
    public function __construct(private array $sourceConfigs, private PostTypeSyncInProgressInterface $inProgress)
    {
    }

    public function addHooks(): void
    {
        add_action('wp_ajax_' . self::$action, [$this, 'handleRequest']);
    }

    public function handleRequest(): void
    {
        $postType = $_REQUEST['post_type'] ?? null;
        $postId   = $_REQUEST['post_id'] ?? null;

        if (empty($postType)) {
            throw new \InvalidArgumentException('Missing post_type parameter');
        }

        require_once(ABSPATH . 'wp-admin/includes/media.php');
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/image.php');

        $progressReporter = new \Municipio\ProgressReporter\SseProgressReporterService(new HttpHeader());
        $syncHandler      = new SyncHandler($this->sourceConfigs, WpService::get(), $progressReporter);
        $progressReporter->start();

        if ($this->inProgress->isInProgress($postType)) {
            $progressReporter->finish('Sync already in progress.');
            return;
        }

        $this->inProgress->setInProgress($postType, true);

        $syncHandler->sync($postType, $postId);

        $this->inProgress->setInProgress($postType, false);

        $progressReporter->finish('Sync completed. Reload page to see changes.');
    }
}
