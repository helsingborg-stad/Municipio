<?php

namespace Municipio\ExternalContent\Sync;

class NullSync implements SyncSourceToLocalInterface
{
    public function __construct()
    {
    }

    /**
     * @inheritDoc
     */
    public function sync(): void
    {
    }
}
