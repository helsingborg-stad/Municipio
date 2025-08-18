<?php

namespace Municipio\Controller\Media;

use Municipio\HooksRegistrar\Hookable;
use WpService\Contracts\{
    AddAction,
    AdminUrl,
    WpDie,
    WpRedirect,
    WpUntrashPost,
    WpVerifyNonce
};

class RestoreMedia implements Hookable
{
    public function __construct(private AddAction&WpDie&WpVerifyNonce&WpRedirect&WpUntrashPost&AdminUrl $wpService)
    {}

    public function addHooks(): void
    {
        $this->wpService->addAction('admin_post_restore_media', array($this, 'handleRestoreMedia'));
    }

    public function handleRestoreMedia(): void
    {
        if (!isset($_GET['attachment_id']) || !is_numeric($_GET['attachment_id'])) {
            $this->wpService->wpDie('Invalid attachment ID');
        }

        $attachmentId = intval($_GET['attachment_id']);

        // Verify nonce
        if (!isset($_GET['_wpnonce']) || !$this->wpService->wpVerifyNonce($_GET['_wpnonce'], 'restore_media_' . $attachmentId)) {
            $this->wpService->wpDie('Nonce verification failed');
        }

        // Restore the media from trash
        $result = $this->wpService->wpUntrashPost($attachmentId);

        if ($result) {
            $this->wpService->wpRedirect($this->wpService->adminUrl('upload.php?page=media-trash&restored=1'));
            exit;
        } else {
            $this->wpService->wpDie('Failed to restore media.');
        }
    }
}