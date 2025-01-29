<?php

namespace Municipio\ExternalContent\Sync\Triggers;

interface TriggerSyncInterface
{
    /**
     * Triggers the synchronization of external content.
     *
     * @param string $postType The post type to sync.
     * @param int|null $postId The post id to sync.
     * @return void
     */
    public function trigger(string $postType, ?int $postId = null): void;
}
