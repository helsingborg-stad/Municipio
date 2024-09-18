<?php

namespace Municipio\ExternalContent\Sync;

interface SyncBuilderInterface
{
    /**
     * Build the SyncInterface.
     *
     * @return SyncInterface
     */
    public function build(): SyncSourceToLocalInterface;
}
