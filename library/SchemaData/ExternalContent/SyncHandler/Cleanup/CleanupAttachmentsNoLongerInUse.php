<?php

namespace Municipio\SchemaData\ExternalContent\SyncHandler\Cleanup;

use Municipio\SchemaData\ExternalContent\SyncHandler\SyncHandler;
use Municipio\HooksRegistrar\Hookable;
use Municipio\SchemaData\ExternalContent\SyncHandler\SchemaObjectProcessor\ImageSideloadSchemaObjectProcessor;
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
        $metaKey            = ImageSideloadSchemaObjectProcessor::META_KEY_IMAGE_ID;
        $allSchemaObjectIds = $this->getAllSchemaObjectIds();

        $attachmentIds = get_posts([
            'post_type'           => 'attachment',
            'meta_key'            => $metaKey,
            'meta_compare'        => 'EXISTS',
            'post_parent__not_in' => $allSchemaObjectIds,
            'fields'              => 'ids',
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
