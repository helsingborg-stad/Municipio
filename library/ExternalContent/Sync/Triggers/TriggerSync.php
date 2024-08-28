<?php

namespace Municipio\ExternalContent\Sync\Triggers;

use WpService\Contracts\DoAction;

class TriggerSync
{
    public function __construct(
        private DoAction $wpService
    ) {
    }

    protected function trigger(string $postType, ?int $postId = null): void
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
