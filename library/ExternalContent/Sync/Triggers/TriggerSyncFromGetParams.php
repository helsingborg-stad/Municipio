<?php

namespace Municipio\ExternalContent\Sync\Triggers;

use Municipio\HooksRegistrar\Hookable;
use WpService\Contracts\AddAction;
use WpService\Contracts\DoAction;
use WpService\Contracts\VerifyNonce;
use WpService\Contracts\SafeRedirect;

class TriggerSyncFromGetParams extends TriggerSync implements Hookable
{
    public const GET_PARAM_TRIGGER   = 'sync_external_content';
    public const GET_PARAM_POST_TYPE = 'post_type';
    public const GET_PARAM_POST_ID   = 'sync_post_id';

    public function __construct(
        private AddAction&DoAction&VerifyNonce&SafeRedirect $wpService
    ) {
        parent::__construct($wpService);
    }

    public function addHooks(): void
    {
        $this->wpService->addAction('admin_init', array($this, 'tryToTriggerSync'));
    }

    public function tryToTriggerSync(): void
    {
        if (!$this->shouldTrigger()) {
            return;
        }

        $postType = $_GET[self::GET_PARAM_POST_TYPE];
        $postId   = isset($_GET[self::GET_PARAM_POST_ID]) ? $_GET[self::GET_PARAM_POST_ID] : null;

        $this->trigger($postType, $postId);

        $this->wpService->safeRedirect($_SERVER['HTTP_REFERER']);
        exit;
    }

    private function shouldTrigger(): bool
    {
        return
            isset($_GET[self::GET_PARAM_TRIGGER]) &&
            $this->wpService->verifyNonce($_GET['_wpnonce']);
    }
}
