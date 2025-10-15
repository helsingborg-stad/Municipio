<?php

namespace Modularity\Module\Posts\Helper\GetPosts\UserGroupResolver;

use WpService\Contracts\GetCurrentSite;
use WpService\Contracts\GetMainSiteId;
use WpService\Contracts\GetTerm;
use WpService\Contracts\GetUserMeta;
use WpService\Contracts\RestoreCurrentBlog;
use WpService\Contracts\SwitchToBlog;

class UserGroupResolver implements UserGroupResolverInterface
{
    private const TAXONOMY_NAME = 'user_group';

    public function __construct(private GetUserMeta&GetMainSiteId&SwitchToBlog&GetTerm&RestoreCurrentBlog&GetCurrentSite $wpService)
    {
    }

    /**
     * Get the user group slug for the current user.
     *
     * @return string|null The user group slug or null if not found.
     */
    public function getUserGroup(): ?string
    {
        $currentBlogId  = ((int) $this->wpService->getCurrentBlogId()) ?? null;
        $mainBlogId     = ((int) $this->wpService->getMainSiteId()) ?? null;
        $userGroupId    = null;

        if ($currentBlogId !== $mainBlogId) {
            $this->wpService->switchToBlog($mainBlogId);
                $userGroupId = $this->getUserGroupFromBlog();
            $this->wpService->restoreCurrentBlog();
            return $userGroupId;
        }

        return $this->getUserGroupFromBlog();
    }

    /**
     * Get the user group. 
     *
     * @return string|null The user group slug or null if not found.
     */
    private function getUserGroupFromBlog(): ?string
    {
        $terms = $this->wpService->wpGetObjectTerms(
            $this->wpService->getCurrentUserId(),
            self::TAXONOMY_NAME
        );

        return (!empty($terms) && $terms[0] instanceof \WP_Term) ? $terms[0]->slug : null;
    }
}