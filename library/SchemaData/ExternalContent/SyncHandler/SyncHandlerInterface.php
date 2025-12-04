<?php

namespace Municipio\SchemaData\ExternalContent\SyncHandler;

interface SyncHandlerInterface
{
    /**
     * Syncs external content.
     */
    public function sync(string $postType, ?int $postId = null): void;
}
