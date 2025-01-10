<?php

namespace Municipio\Admin\Private;

use WpService\Contracts\AddAction;
use WpService\Contracts\WpGetCurrentUser;
use WpService\Contracts\WpGetPostTerms;
use WP_User;
use WpService\Contracts\IsWpError;

class UserGroupRestriction
{
    private string $userGroupMetaKey  = 'user-group-visibility';
    private string $userGroupTaxonomy = 'user_group';

    /**
     * UserGroupRestriction class constructor.
     *
     * @param AddAction $wpService An instance of the AddAction class.
     * @param WpGetCurrentUser $wpService An instance of the WpGetCurrentUser class.
     * @param WpGetPostTerms $wpService An instance of the WpGetPostTerms class.
     * @param IsWpError $wpService An instance of the IsWpError class.
     */
    public function __construct(private AddAction&WpGetCurrentUser&WpGetPostTerms&IsWpError $wpService)
    {
        $this->wpService->addAction('pre_get_posts', array($this, 'restrictPosts'));
    }

    /**
     * Retrieves the current user.
     *
     * @return WP_User The current user object.
     */
    private function getUser(): WP_User
    {
        static $user;

        if (!$user) {
            $user = $this->wpService->wpGetCurrentUser();
        }

        return $user;
    }

    /**
     * Retrieves the user groups for a given user.
     *
     * @param WP_User $user The user object.
     * @return string|null The user groups as a string or null if no user groups are found.
     */
    private function getUserGroups(WP_User $user): ?string
    {
        static $userGroups;

        if (!$userGroups) {
            $userGroups = $this->wpService->wpGetPostTerms($user->ID, $this->userGroupTaxonomy);

            if ($this->wpService->isWpError($userGroups) || empty($userGroups)) {
                return null;
            }

            $userGroups = array_map(function ($term) {
                return $term->slug;
            }, $userGroups);
        }

        return $userGroups[0] ?? null;
    }

    /**
     * Restricts the posts in a query based on user group restrictions.
     *
     * @param WP_Query $query The WP_Query object representing the current query.
     * @return void
     */
    public function restrictPosts($query)
    {
        $user = $this->getUser();

        if (empty($user->ID)) {
            return;
        }

        $userGroup  = $this->getUserGroups($user);
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
                'key'     => $this->userGroupMetaKey,
                'compare' => 'NOT EXISTS',
            ],
            [
                'key'     => $this->userGroupMetaKey,
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
