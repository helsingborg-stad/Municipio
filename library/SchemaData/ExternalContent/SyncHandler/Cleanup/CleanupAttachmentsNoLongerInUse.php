<?php

namespace Municipio\SchemaData\ExternalContent\SyncHandler\Cleanup;

use Municipio\SchemaData\ExternalContent\SyncHandler\SyncHandler;
use Municipio\HooksRegistrar\Hookable;
use Municipio\SchemaData\ExternalContent\SyncHandler\SchemaObjectProcessor\ImageSideloadSchemaObjectProcessor;
use Municipio\SchemaData\ExternalContent\WpPostArgsFromSchemaObject\ConnectUploadedImagesToPost;
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
        foreach ($this->getAllAttachmentIdsNotInUse() as $attachmentId) {
            $this->wpService->wpDeleteAttachment($attachmentId, true);
        }
    }

    /**
     * Get all attachment ids that are not in use.
     */
    private function getAllAttachmentIdsNotInUse(): array
    {
        $metaKey         = ImageSideloadSchemaObjectProcessor::META_KEY_IMAGE_ID;
        $connectedImages = $this->getAllConnectedImages();
        $query           = $this->db->prepare("SELECT post_id FROM %i WHERE meta_key = %s AND meta_value NOT IN ({$connectedImages})", $this->db->postmeta, $metaKey);

        return array_map(fn($row) => $row->post_id, $this->db->get_results($query));
    }

    /**
     * Get all connected image IDs.
     */
    private function getAllConnectedImages(): string
    {
        $metaKey = ConnectUploadedImagesToPost::META_KEY;

        $query   = $this->db->prepare("SELECT meta_value FROM %i WHERE meta_key = %s", $this->db->postmeta, $metaKey);
        $results = $this->db->get_results($query);
        $results = array_map(fn($item) => maybe_unserialize($item->meta_value), $results);

        // merge all arrays in $results
        $results = array_merge(...array_filter($results, fn($item) => is_array($item)));

        return implode(',', array_map(fn($item) => "'" . esc_sql($item) . "'", $results));
    }
}
