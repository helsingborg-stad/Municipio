<?php

namespace Municipio\Admin\Private;

use Municipio\HooksRegistrar\Hookable;
use WpService\Contracts\AddAction;
use WpService\Contracts\WpGetCurrentUser;
use WpService\Contracts\WpGetPostTerms;
use WpService\Contracts\IsUserLoggedIn;
use WpService\Contracts\IsWpError;
use Municipio\Helper\User\User;
use Municipio\Admin\Private\Config\UserGroupRestrictionConfig;

/**
 * UserGroupRestriction class.
 *
 * This class is responsible for handling user group restrictions.
 * It is located in the file UserGroupRestriction.php in the directory /workspaces/municipio-deployment/wp-content/themes/municipio/library/Admin/Private/.
 */
class UserGroupRestriction implements Hookable
{
    private string $userGroupMetaKey = 'user-group-visibility';

    /**
     * UserGroupRestriction class constructor.
     *
     * @param AddAction $wpService An instance of the AddAction class.
     * @param WpGetCurrentUser $wpService An instance of the WpGetCurrentUser class.
     * @param WpGetPostTerms $wpService An instance of the WpGetPostTerms class.
     * @param IsWpError $wpService An instance of the IsWpError class.
     */
    public function __construct(
        private AddAction&IsUserLoggedIn&WpGetPostTerms&IsWpError $wpService,
        private User $userHelper,
        private UserGroupRestrictionConfig $userGroupRestrictionConfig
    ) {
    }

    /**
     * Adds hooks for the UserGroupSelector class.
     *
     * This method adds hooks for the UserGroupSelector class.
     *
     * @return void
     */
    public function addHooks(): void
    {
        $this->wpService->addAction('pre_get_posts', array($this, 'restrictPosts'), 1);
    }

    /**
     * Restricts the posts in a query based on user group restrictions.
     *
     * @param WP_Query $query The WP_Query object representing the current query.
     * @return void
     */
    public function restrictPosts($query): void
    {
        if (!$this->wpService->isUserLoggedIn()) {
            return;
        }

        // Set user & get user group
        $this->userHelper->setUser();
        $userGroup = $this->userHelper->getUserGroup();
        $userGroup = $userGroup->slug ?? null;

        $postStatus = $query->get('post_status');

        if (!$this->canHavePrivatePosts($postStatus)) {
            return;
        }

        $metaQuery = $query->get('meta_query');

        if (empty($metaQuery)) {
            $metaQuery = [];
        }

        $metaQuery[] = [
            'relation' => 'OR',
            [
                'key'     => $this->userGroupRestrictionConfig->getUserGroupVisibilityMetaKey(),
                'compare' => 'NOT EXISTS',
            ],
            [
                'key'     => $this->userGroupRestrictionConfig->getUserGroupVisibilityMetaKey(),
                'value'   => $userGroup,
                'compare' => 'IN',
            ]
        ];

        $query->set('meta_query', $metaQuery);
    }

    /**
     * Determines if a post can have private status.
     *
     * @param string|array $postStatus The status of the post(s).
     * @return bool Returns true if the post(s) can have private status, false otherwise.
     */
    private function canHavePrivatePosts(string|array $postStatus): bool
    {
        $postStatus = is_array($postStatus) ? $postStatus : [$postStatus];

        return in_array('private', $postStatus, true);
    }
}
