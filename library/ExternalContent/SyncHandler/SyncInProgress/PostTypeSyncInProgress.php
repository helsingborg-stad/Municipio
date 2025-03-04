<?php

namespace Municipio\ExternalContent\SyncHandler\SyncInProgress;

use WpService\Contracts\GetTransient;
use WpService\Contracts\DeleteTransient;
use WpService\Contracts\SetTransient;

/**
 * class PostTypeSyncInProgress
 */
class PostTypeSyncInProgress implements PostTypeSyncInProgressInterface
{
    public const TRANSIENT_PREFIX  = 'sync_in_progress_';
    public const TRANSIENT_TIMEOUT = 60 * 5; // 5 minutes

    /**
     * Constructor
     */
    public function __construct(private GetTransient&DeleteTransient&SetTransient $wpService)
    {
    }

    /**
     * @inheritDoc
     */
    public function isInProgress(string $postType): bool
    {
        return (bool) $this->wpService->getTransient($this->getTransientName($postType));
    }

    /**
     * @inheritDoc
     */
    public function setInProgress(string $postType, bool $inProgress): void
    {
        $inProgress
            ? $this->wpService->setTransient($this->getTransientName($postType), true, self::TRANSIENT_TIMEOUT)
            : $this->wpService->deleteTransient($this->getTransientName($postType));
    }

    /**
     * Returns the transient name for the given post type.
     *
     * @param string $postType
     * @return string
     */
    public function getTransientName(string $postType): string
    {
        return self::TRANSIENT_PREFIX . $postType;
    }
}
