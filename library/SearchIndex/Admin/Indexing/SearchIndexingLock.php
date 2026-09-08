<?php

declare(strict_types=1);

namespace Municipio\SearchIndex\Admin\Indexing;

use WpService\Contracts\AddOption;
use WpService\Contracts\DeleteOption;
use WpService\Contracts\GetOption;
use WpService\Contracts\UpdateOption;

/**
 * Stores a site-scoped, expiring indexing lock in the WordPress options table.
 */
class SearchIndexingLock implements SearchIndexingLockInterface
{
    private const OPTION_NAME = 'municipio_search_index_admin_indexing_lock';
    private const TIMEOUT_SECONDS = 900;

    /**
     * Create an indexing lock backed by atomic WordPress option insertion.
     */
    public function __construct(
        private AddOption&DeleteOption&GetOption&UpdateOption $wpService,
    ) {}

    /**
     * Acquire the indexing lock for a new owner.
     */
    public function acquire(string $owner): bool
    {
        $lock = $this->createLock($owner);

        if ($this->wpService->addOption(self::OPTION_NAME, $lock, '', false)) {
            return true;
        }

        $existingLock = $this->wpService->getOption(self::OPTION_NAME, []);

        if (!$this->isExpired($existingLock)) {
            return false;
        }

        $this->wpService->deleteOption(self::OPTION_NAME);

        return $this->wpService->addOption(self::OPTION_NAME, $lock, '', false);
    }

    /**
     * Extend the lock while its owner is still indexing.
     */
    public function refresh(string $owner): void
    {
        $existingLock = $this->wpService->getOption(self::OPTION_NAME, []);

        if (($existingLock['owner'] ?? null) === $owner) {
            $this->wpService->updateOption(self::OPTION_NAME, $this->createLock($owner), false);
        }
    }

    /**
     * Release the lock when owned by the supplied owner.
     */
    public function release(string $owner): void
    {
        $existingLock = $this->wpService->getOption(self::OPTION_NAME, []);

        if (($existingLock['owner'] ?? null) === $owner) {
            $this->wpService->deleteOption(self::OPTION_NAME);
        }
    }

    /**
     * Create the persisted lock payload.
     *
     * @return array{owner: string, expiresAt: int}
     */
    private function createLock(string $owner): array
    {
        return [
            'owner' => $owner,
            'expiresAt' => time() + self::TIMEOUT_SECONDS,
        ];
    }

    /**
     * Determine whether a persisted lock may be reclaimed.
     */
    private function isExpired(mixed $lock): bool
    {
        return !is_array($lock) || (int) ($lock['expiresAt'] ?? 0) < time();
    }
}