<?php

namespace Municipio\ExternalContent\Sync;

interface ISyncSourceToLocal
{
    /**
     * Syncs from source to local.
     */
    public function sync(): void;
}
