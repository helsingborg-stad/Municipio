<?php

namespace Municipio\ExternalContent\Sync\Triggers;

interface InProgressInterface
{
    /**
     * Check if sync is in progress.
     *
     * @return bool
     */
    public function isInProgress(): bool;

    /**
     * Set if sync is in progress.
     *
     * @param bool $inProgress
     */
    public function setInProgress(bool $inProgress): void;
}
