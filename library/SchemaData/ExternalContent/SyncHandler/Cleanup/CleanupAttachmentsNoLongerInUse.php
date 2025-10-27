<?php

namespace Municipio\SchemaData\ExternalContent\SyncHandler\Cleanup;

use Municipio\SchemaData\ExternalContent\SyncHandler\SchemaObjectProcessor\ImageSideloadSchemaObjectProcessor;
use wpdb;
use WpService\Contracts\WpDeleteAttachment;

/**
 * Cleanup attachments that are no longer in use.
 */
class CleanupAttachmentsNoLongerInUse
{
    /**
     * Constructor.
     */
    public function __construct(
        private WpDeleteAttachment $wpService,
        private wpdb $db
    ) {
    }

    /**
     * Cleanup attachments that are no longer in use.
     *
     * @return void
     */
    public function cleanup(): void
    {
        foreach ($this->getAllAttachmentIdsNotInUse() as $attachmentId) {
            $this->wpService->wpDeleteAttachment($attachmentId, true);
        }
    }

    /**
     * Get all attachment ids that are not in use.
     */
    private function getAllAttachmentIdsNotInUse(): array
    {
        $allSchemaObjectIds = $this->getAllSchemaObjectIds();

        $attachmentIds = get_posts([
            'post_type'   => 'attachment',
            'meta_query'  => [
                'relation' => 'AND',
                [
                    'key'     => ImageSideloadSchemaObjectProcessor::META_KEY_IMAGE_ID,
                    'compare' => 'EXISTS',
                ],
                [
                    'key'     => ImageSideloadSchemaObjectProcessor::META_KEY_SCHEMA_PARENT_ID,
                    'compare' => 'NOT IN',
                    'value'   => $allSchemaObjectIds,
                ],
            ],
            'fields'      => 'ids',
            'numberposts' => -1,
        ]);

        return $attachmentIds;
    }

    /**
     * Get all connected image IDs.
     */
    private function getAllSchemaObjectIds(): array
    {
        $query   = $this->db->prepare("SELECT meta_value FROM %i WHERE meta_key = %s", $this->db->postmeta, 'originId');
        $results = $this->db->get_results($query);
        return array_map(fn($item) => maybe_unserialize($item->meta_value), $results);
    }
}
