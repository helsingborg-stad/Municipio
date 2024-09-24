<?php

namespace Municipio\ExternalContent\Sync\Triggers;

use Municipio\HooksRegistrar\Hookable;
use WpService\Contracts\AddAction;
use WpService\Contracts\DoAction;
use WpService\Contracts\VerifyNonce;
use WpService\Contracts\SafeRedirect;

/**
 * Class TriggerSyncFromGetParams
 *
 * This class triggers synchronization of external content based on GET parameters.
 */
class TriggerSyncFromGetParams extends TriggerSync implements Hookable
{
    public const GET_PARAM_TRIGGER   = 'sync_external_content';
    public const GET_PARAM_POST_TYPE = 'post_type';
    public const GET_PARAM_POST_ID   = 'sync_post_id';

    /**
     * Constructor for TriggerSyncFromGetParams.
     *
     * @param AddAction&DoAction&VerifyNonce&SafeRedirect $wpService The WordPress service.
     */
    public function __construct(
        private AddAction&DoAction&VerifyNonce&SafeRedirect $wpService
    ) {
        parent::__construct($wpService);
    }

    /**
     * @inheritDoc
     */
    public function addHooks(): void
    {
        $this->wpService->addAction('admin_init', array($this, 'tryToTriggerSync'));
    }

    /**
     * Tries to trigger the synchronization process based on GET parameters.
     *
     * @return void
     */
    public function tryToTriggerSync(): void
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

        $this->wpService->safeRedirect($_SERVER['HTTP_REFERER']);
        exit;
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
            $this->wpService->verifyNonce($_GET['_wpnonce']);
    }
}
