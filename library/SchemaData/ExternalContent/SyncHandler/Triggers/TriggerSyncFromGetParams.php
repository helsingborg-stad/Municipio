<?php

namespace Municipio\SchemaData\ExternalContent\SyncHandler\Triggers;

use Municipio\HooksRegistrar\Hookable;
use WpService\Contracts\AddAction;
use WpService\Contracts\DoAction;
use WpService\Contracts\GetOption;
use WpService\Contracts\UpdateOption;
use WpService\Contracts\WpSafeRedirect;
use WpService\Contracts\WpVerifyNonce;

/**
 * Class TriggerSyncFromGetParams
 *
 * This class triggers synchronization of external content based on GET parameters.
 */
class TriggerSyncFromGetParams implements TriggerSyncInterface, Hookable
{
    public const GET_PARAM_TRIGGER   = 'sync_external_content';
    public const GET_PARAM_POST_TYPE = 'post_type';
    public const GET_PARAM_POST_ID   = 'sync_post_id';

    /**
     * Constructor.
     */
    public function __construct(
        private AddAction&DoAction&WpVerifyNonce&WpSafeRedirect&UpdateOption&GetOption $wpService,
        private TriggerSyncInterface $inner,
    ) {
    }

    /**
     * @inheritDoc
     */
    public function addHooks(): void
    {
        $this->wpService->addAction('admin_init', array($this, 'tryToTrigger'));
    }

    /**
     * Attempts to trigger the synchronization process if conditions are met.
     *
     * @return void
     */
    public function tryToTrigger(): void
    {
        if (!$this->shouldTrigger() || !isset($_GET[self::GET_PARAM_POST_TYPE])) {
            return;
        }

        $postType = $_GET[self::GET_PARAM_POST_TYPE];
        $postId   = isset($_GET[self::GET_PARAM_POST_ID]) ? $_GET[self::GET_PARAM_POST_ID] : null;

        $this->trigger($postType, $postId);

        if (empty($_SERVER['HTTP_REFERER'])) {
            return;
        }

        $this->wpService->wpSafeRedirect($_SERVER['HTTP_REFERER']);
        exit;
    }

    /**
     * Tries to trigger the synchronization process based on GET parameters.
     *
     * @return void
     */
    public function trigger(string $postType, ?int $postId = null): void
    {
        $this->inner->trigger($postType, $postId);
    }

    /**
     * Determines if the synchronization should be triggered based on GET parameters.
     *
     * @return bool True if the synchronization should be triggered, false otherwise.
     */
    private function shouldTrigger(): bool
    {
        return
            isset($_GET[self::GET_PARAM_TRIGGER]) &&
            !empty($_GET['_wpnonce']) &&
            $this->wpService->wpVerifyNonce($_GET['_wpnonce'], -1);
    }
}
