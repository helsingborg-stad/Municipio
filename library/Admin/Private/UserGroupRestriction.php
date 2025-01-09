<?php

namespace Municipio\Admin\Private;

use WpService\Contracts\AddAction;
use WpService\Contracts\WpGetCurrentUser;
use WpService\Contracts\WpGetPostTerms;
use WP_User;
use WpService\Contracts\IsWpError;

class UserGroupRestriction
{
    private string $userGroupMetaKey = 'user-group-visibility';
    private string $userGroupTaxonomy = 'user_group';

    public function __construct(private AddAction&WpGetCurrentUser&WpGetPostTerms&IsWpError $wpService)
    {
        $this->wpService->addAction('pre_get_posts', array($this, 'restrictPosts'));
    }

    private function getUser(): WP_User
    {
        static $user;

        if (!$user) {
            $user = $this->wpService->wpGetCurrentUser();
        }

        return $user;
    }

    private function getUserGroups(WP_User $user): ?string
    {
        static $userGroups;

        if (!$userGroups) {
            $userGroups = $this->wpService->wpGetPostTerms($user->ID, $this->userGroupTaxonomy);

            if ($this->wpService->isWpError($userGroups) || empty($userGroups)) {
                return ""; // Return an empty string if no terms are found or there's an error
            }

            $userGroups = array_map(function ($term) {
                return $term->slug;
            }, $userGroups);
        }

        // Return the first value, or an empty string if $userGroups is empty
        return $userGroups[0] ?? null;
    }

    public function restrictPosts($query)
    {
        $user = $this->getUser();
        
        if (empty($user->ID)) {
            return;
        }
        
        $userGroup = $this->getUserGroups($user);
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
                'key' => $this->userGroupMetaKey,
                'compare' => 'NOT EXISTS',
            ],
            [
                'key' => $this->userGroupMetaKey,
                'value' => $userGroup,
                'compare' => 'IN',
            ]
        ];

        $query->set('meta_query', $metaQuery);
    }

    private function canHavePrivatePosts(string|array $postStatus): bool
    {
        $postStatus = is_array($postStatus) ? $postStatus : [$postStatus];

        return in_array('private', $postStatus, true);
    }
}