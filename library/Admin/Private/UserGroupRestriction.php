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

    private function getUserGroups(WP_User $user): array
    {
        static $userGroups;

        if (!$userGroups) {
            $userGroups = $this->wpService->wpGetPostTerms($user->ID, $this->userGroupTaxonomy);

            if ($this->wpService->isWpError($userGroups) || empty($userGroups)) {
                $userGroups = [];
                return $userGroups;
            }

            $userGroups = array_map(function ($term) {
                return $term->slug;
            }, $userGroups);
        }

        return $userGroups;
    }

    public function restrictPosts($query)
    {
        $user = $this->getUser();
        
        if (empty($user->ID)) {
            return;
        }
        
        $userGroups = $this->getUserGroups($user);
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
                'value' => $userGroups,
                'compare' => 'IN',
            ],
            [
                'key' => $this->userGroupMetaKey,
                'compare' => 'NOT EXISTS',
            ],
        ];

        $query->set('meta_query', $metaQuery);
    }

    private function canHavePrivatePosts(string|array $postStatus): bool
    {
        $postStatus = is_array($postStatus) ? $postStatus : [$postStatus];

        return in_array('private', $postStatus, true);
    }
}