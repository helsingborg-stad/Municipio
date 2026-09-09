<?php

namespace Municipio\SchemaData\ExternalContent\SyncHandler;

interface SyncHandlerInterface
{
    /**
     * Syncs external content.
     *
     * @param bool $force When true, re-sync every source object even if its checksum is unchanged since last sync.
     */
    public function sync(string $postType, ?int $postId = null, bool $force = false): void;
}
