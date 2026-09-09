<?php

declare(strict_types=1);

namespace Municipio\SchemaData\ExternalContent\Cli;

use Municipio\SchemaData\ExternalContent\Config\SourceConfigInterface;
use Municipio\SchemaData\ExternalContent\SyncHandler\SyncHandlerInterface;
use Municipio\SchemaData\ExternalContent\SyncHandler\SyncInProgress\PostTypeSyncInProgressInterface;

/**
 * Syncs a post type from its configured external content source.
 */
class SyncExternalContentCommand
{
    /**
     * @param SourceConfigInterface[] $sourceConfigs
     */
    public function __construct(
        private array $sourceConfigs,
        private SyncHandlerInterface $syncHandler,
        private PostTypeSyncInProgressInterface $inProgress,
    ) {
    }

    /**
     * Register the WP-CLI sync command.
     */
    public function register(): void
    {
        $this->callWpCli('add_command', 'municipio external-content sync', [$this, 'sync']);
    }

    /**
     * Sync a post type from its configured external content source.
     *
     * ## OPTIONS
     *
     * <post_type>
     * : The post type to sync, as listed by `wp municipio external-content list`.
     *
     * [--force]
     * : Re-sync every object, even ones whose source data checksum is unchanged since the last sync.
     *
     * ## EXAMPLES
     *
     *     wp municipio external-content sync place
     *     wp municipio external-content sync place --force
     */
    public function sync(array $arguments, array $associativeArguments): void
    {
        $postType = $arguments[0] ?? null;

        if (!is_string($postType) || $postType === '' || !$this->hasSourceConfigForPostType($postType)) {
            $this->callWpCli('error', sprintf(
                'No external content source is configured for post type "%s".',
                is_string($postType) ? $postType : '',
            ));
            return;
        }

        if ($this->inProgress->isInProgress($postType)) {
            $this->callWpCli('error', sprintf('Sync already in progress for post type "%s".', $postType));
            return;
        }

        $force = isset($associativeArguments['force']);

        $this->inProgress->setInProgress($postType, true);

        try {
            $this->callWpCli('log', sprintf(
                'Syncing "%s" from remote source%s...',
                $postType,
                $force ? ' (forcing update of all posts)' : '',
            ));
            $this->syncHandler->sync($postType, null, $force);
        } finally {
            $this->inProgress->setInProgress($postType, false);
        }

        $this->callWpCli('success', sprintf('Sync completed for post type "%s".', $postType));
    }

    /**
     * Check whether a source config exists for the given post type.
     */
    private function hasSourceConfigForPostType(string $postType): bool
    {
        foreach ($this->sourceConfigs as $config) {
            if ($config->getPostType() === $postType) {
                return true;
            }
        }

        return false;
    }

    /**
     * Invoke the WP-CLI runtime without requiring its classes in web requests.
     */
    private function callWpCli(string $method, mixed ...$arguments): mixed
    {
        return call_user_func_array(['WP_CLI', $method], $arguments);
    }
}
