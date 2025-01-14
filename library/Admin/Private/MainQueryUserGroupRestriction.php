<?php

namespace Municipio\Admin\Private;

use WpService\Contracts\AddAction;
use WpService\Contracts\GetPostMeta;
use WpService\Contracts\GetQueriedObjectId;
use WpService\Contracts\IsPostPubliclyViewable;
use WpService\Contracts\IsUserLoggedIn;
use Municipio\Helper\User\User;
use Municipio\Admin\Private\Config\UserGroupRestrictionConfig;

/**
 * UserGroupRestrictionMainQuery class.
 *
 * This class implements the Hookable interface and is responsible for handling user group restrictions in the main query.
 */
class MainQueryUserGroupRestriction
{
    /**
     * Class MainQueryUserGroupRestriction
     *
     * This class handles the user group restriction for the main query.
     */
    public function __construct(
        private AddAction&IsUserLoggedIn&IsPostPubliclyViewable&GetPostMeta&GetQueriedObjectId $wpService,
        private User $userHelper,
        private UserGroupRestrictionConfig $userGroupRestrictionConfig
    ) {
    }

    /**
     * Determines whether the current user should have restricted access to a specific post.
     *
     * @param int|null $postId The ID of the post to check for restriction. If null, the function will return false.
     * @return bool Returns true if the current user should have restricted access to the post, false otherwise.
     */
    public function shouldRestrict(?int $postId): bool
    {
        // Check if post ID is set
        if (empty($postId)) {
            return false;
        }

        // Check if user is logged in or post is publicly viewable
        if (!$this->wpService->isUserLoggedIn() || $this->wpService->isPostPubliclyViewable()) {
            return false;
        }

        // Set user & get user group
        $this->userHelper->setUser();
        $userGroup = $this->userHelper->getUserGroup();
        $userGroup = $userGroup->slug ?? null;

        // Get post user group meta
        $postUserGroupMeta = $this->wpService->getPostMeta(
            $postId,
            $this->userGroupRestrictionConfig->getUserGroupVisibilityMetaKey()
        );

        //Check for matching user group
        if (!empty($postUserGroupMeta) && !in_array($userGroup, $postUserGroupMeta)) {
            return true;
        }

        return false;
    }
}
