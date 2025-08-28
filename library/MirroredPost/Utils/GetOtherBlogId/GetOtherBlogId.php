<?php

namespace Municipio\MirroredPost\Utils\GetOtherBlogId;

use WpService\Contracts\{
    GetCurrentBlogId,
    GetQueryVar,
    IsMultisite,
    MsIsSwitched
};

/**
 * Retrieves the ID of another blog in a multisite WordPress installation.
 */
class GetOtherBlogId implements GetOtherBlogIdInterface
{
    /**
     * Constructor.
     *
     * @param IsMultisite&MsIsSwitched&GetQueryVar&GetCurrentBlogId $wpService
     */
    public function __construct(
        private IsMultisite&MsIsSwitched&GetQueryVar&GetCurrentBlogId $wpService
    ) {
    }

    /**
     * Retrieves the ID of another blog if the current post is from a different blog.
     *
     * @return int|null The ID of the other blog, or null if not applicable.
     */
    public function getOtherBlogId(): ?int
    {
        if ($this->isInSwitchedState()) {
            return $this->wpService->getCurrentBlogId();
        }

        return null;
    }

    /**
     * Checks if the current WordPress installation is in a switched state.
     *
     * @return bool True if in switched state, false otherwise.
     */
    private function isInSwitchedState(): bool
    {
        return $this->wpService->isMultiSite()
            && $this->wpService->msIsSwitched();
    }
}
