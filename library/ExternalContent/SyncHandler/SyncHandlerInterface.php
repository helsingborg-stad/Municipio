<?php

namespace Municipio\ExternalContent\SyncHandler;

interface SyncHandlerInterface
{
    /**
     * Synchronizes external content with local system.
     *
     * @return void
     */
    public function sync(): void;
}
