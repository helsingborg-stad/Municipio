<?php

namespace Municipio\SchemaData\ExternalContent\SyncHandler\SyncInProgress;

interface PostTypeSyncInProgressInterface
{
    /**
     * Returns true if synchronization is in progress for the given post type.
     *
     * @param string $postType
     * @return bool
     */
    public function isInProgress(string $postType): bool;

    /**
     * Sets the synchronization status for the given post type.
     *
     * @param string $postType
     * @param bool $inProgress
     */
    public function setInProgress(string $postType, bool $inProgress): void;
}
