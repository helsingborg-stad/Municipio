<?php

namespace Municipio\ExternalContent\Sync;

interface SyncSourceToLocalInterface
{
    /**
     * Syncs from source to local.
     */
    public function sync(): void;
}
