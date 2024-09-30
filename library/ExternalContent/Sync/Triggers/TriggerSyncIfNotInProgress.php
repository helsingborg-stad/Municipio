<?php

namespace Municipio\ExternalContent\Sync\Triggers;

use Municipio\ExternalContent\Sync\SyncInPorgress\PostTypeSyncInProgressInterface;

class TriggerSyncIfNotInProgress implements TriggerSyncInterface
{
    public function __construct(
        private PostTypeSyncInProgressInterface $inProgress,
        private TriggerSyncInterface $inner
    ) {
    }

    public function trigger(string $postType, ?int $postId = null): void
    {
        if ($this->inProgress->isInProgress($postType)) {
            return;
        }

        $this->inProgress->setInProgress($postType, true);

        $this->inner->trigger($postType, $postId);

        $this->inProgress->setInProgress($postType, false);
    }
}
