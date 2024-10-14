<?php

namespace Municipio\ExternalContent\Sync;

use wpdb;
use WpService\Contracts\WpDeleteAttachment;

/**
 * Class PruneAttachmentsNoLongerInSource
 */
class PruneAttachmentsNoLongerInUse implements SyncSourceToLocalInterface
{
    /**
     * Constructor.
     */
    public function __construct(
        private WpDeleteAttachment $wpService,
        private wpdb $db,
        private ?SyncSourceToLocalInterface $inner = null
    ) {
    }

    /**
     * @inheritDoc
     */
    public function sync(): void
    {
        $this->inner?->sync();

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
