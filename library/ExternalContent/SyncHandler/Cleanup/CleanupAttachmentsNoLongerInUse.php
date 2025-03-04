<?php

namespace Municipio\ExternalContent\SyncHandler\Cleanup;

use Municipio\ExternalContent\SyncHandler\SyncHandler;
use Municipio\HooksRegistrar\Hookable;
use wpdb;
use WpService\Contracts\AddAction;
use WpService\Contracts\WpDeleteAttachment;

/**
 * Cleanup attachments that are no longer in use.
 */
class CleanupAttachmentsNoLongerInUse implements Hookable
{
    /**
     * Constructor.
     */
    public function __construct(
        private AddAction&WpDeleteAttachment $wpService,
        private wpdb $db
    ) {
    }

    /**
     * @inheritDoc
     */
    public function addHooks(): void
    {
        $this->wpService->addAction(SyncHandler::ACTION_AFTER, [$this, 'cleanup']);
    }

    /**
     * Cleanup attachments that are no longer in use.
     *
     * @return void
     */
    public function cleanup(): void
    {
        $attachmentIds = $this->getAllAttachmentIdsNotInUse();

        foreach ($attachmentIds as $attachmentId) {
            $this->wpService->wpDeleteAttachment($attachmentId->post_id, true);
        }
    }

    /**
     * Get all attachment ids that are not in use.
     */
    private function getAllAttachmentIdsNotInUse(): array
    {
        $query = <<<SQL
            SELECT post_id 
            FROM {$this->db->postmeta} 
            WHERE meta_key = "synced_from_external_source" 
            AND post_id NOT IN 
                (
                    SELECT meta_value 
                    FROM {$this->db->postmeta} 
                    WHERE meta_key = "_thumbnail_id"
                )
        SQL;

        return $this->db->get_results(
            $this->db->prepare($query, $this->db->postmeta, 'synced_from_external_source', '_thumbnail_id')
        );
    }
}
