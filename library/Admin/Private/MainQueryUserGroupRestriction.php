<?php

namespace Municipio\Admin\Private;

use WpService\Contracts\AddAction;
use WpService\Contracts\GetPostMeta;
use WpService\Contracts\GetQueriedObjectId;
use WpService\Contracts\IsPostPubliclyViewable;
use WpService\Contracts\IsUserLoggedIn;

/**
 * UserGroupRestrictionMainQuery class.
 *
 * This class implements the Hookable interface and is responsible for handling user group restrictions in the main query.
 */
class MainQueryUserGroupRestriction
{
    private string $userGroupMetaKey = 'user-group-visibility';

    /**
     * Class MainQueryUserGroupRestriction
     *
     * This class handles the user group restriction for the main query.
     */
    public function __construct(private AddAction&IsUserLoggedIn&IsPostPubliclyViewable&GetPostMeta&GetQueriedObjectId $wpService)
    {
    }

    /**
     * Determines whether the current user should have restricted access to a specific post.
     *
     * @param int|null $postId The ID of the post to check for restriction. If null, the function will return false.
     * @return bool Returns true if the current user should have restricted access to the post, false otherwise.
     */
    public function shouldRestrict(?int $postId): bool
    {
        if (
            !$this->wpService->isUserLoggedIn() ||
            $this->wpService->isPostPubliclyViewable() ||
            empty($postId)
        ) {
            return false;
        }

        $userGroup         = \Municipio\Admin\Private\Helper\GetUserGroup::getUserGroups();
        $postUserGroupMeta = $this->wpService->getPostMeta(
            $postId,
            $this->userGroupMetaKey
        );

        if (!empty($postUserGroupMeta) && !in_array($userGroup, $postUserGroupMeta)) {
            return true;
        }

        return false;
    }
}
