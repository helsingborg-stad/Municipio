<?php

namespace Municipio\ExternalContent\SyncHandler\Triggers;

use WpService\Contracts\DoAction;

/**
 * Class TriggerSync
 */
class TriggerSync implements TriggerSyncInterface
{
    /**
     * Constructor.
     */
    public function __construct(
        private DoAction $wpService
    ) {
    }

    /**
     * @inheritDoc
     */
    public function trigger(string $postType, ?int $postId = null): void
    {
        /**
         * Fires when external content should be synced.
         *
         * @param string $postType The post type to sync.
         * @param int|null $postId The post id to sync.
         */
        $this->wpService->doAction('Municipio/ExternalContent/Sync', $postType, $postId);
    }
}
