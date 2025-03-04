<?php

namespace Municipio\ExternalContent\SyncHandler\Triggers;

use Municipio\ExternalContent\SyncHandler\SyncInProgress\PostTypeSyncInProgressInterface;

/**
 * Class TriggerSyncIfNotInProgress
 */
class TriggerSyncIfNotInProgress implements TriggerSyncInterface
{
    /**
     * Constructor.
     */
    public function __construct(
        private PostTypeSyncInProgressInterface $inProgress,
        private TriggerSyncInterface $inner
    ) {
    }

    /**
     * @inheritDoc
     */
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
