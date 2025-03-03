<?php

namespace Municipio\ExternalContent\SyncHandler\WpInsertPost;

use WP_Error;
use WpService\Contracts\WpInsertPost;

/**
 * Class InsertPostOnlyIfSyncIsNotAlreadyInProgress
 *
 * This class is responsible for inserting a post only if a synchronization process is not already in progress.
 */
class InsertPostOnlyIfSyncIsNotAlreadyInProgress implements WpInsertPost
{
    /**
     * Constructor for the InsertPostOnlyIfSyncIsNotAlreadyInProgress class.
     *
     * @param WpInsertPost $wpService
     */
    public function __construct(private WpInsertPost $wpService)
    {
    }

    /**
     * @inheritDoc
     */
    public function wpInsertPost(array $postarr, bool $wpError = false, bool $fireAfterHooks = true): int|WP_Error
    {
        return $this->shouldAllowInsert($postarr)
            ? $this->wpService->wpInsertPost($postarr, $wpError, $fireAfterHooks)
            : 0;
    }

    /**
     * Determines if a post insert should be allowed based on whether a sync is already in progress.
     *
     * @param array $postarr The array of post data.
     * @return bool True if the insert should be allowed, false otherwise.
     */
    private function shouldAllowInsert($postarr): bool
    {
        return !($postarr['meta_input']['schemaData']['@preventSync'] ?? false);
    }
}
