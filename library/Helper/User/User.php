<?php

namespace Municipio\Helper\User;

use WpService\Contracts\WpGetCurrentUser;
use WpService\Contracts\WpGetObjectTerms;
use WpService\Contracts\IsWpError;
use WpService\Contracts\GetUserMeta;
use WpService\Contracts\GetBlogDetails;
use AcfService\Contracts\GetField;

use Municipio\Helper\User\Config\UserConfigInterface;

use Municipio\Helper\User\Contracts\SetUser;
use Municipio\Helper\User\Contracts\UserHasRole;
use Municipio\Helper\User\Contracts\GetUserGroup;
use Municipio\Helper\User\Contracts\GetUserGroupUrl;
use Municipio\Helper\User\Contracts\GetUserGroupUrlType;
use Municipio\Helper\User\Contracts\GetUserPrefersGroupUrl;

use Municipio\Helper\User\FieldResolver\UserGroupUrl;
use WP_Term;
use WP_User;

class User implements SetUser, UserHasRole, GetUserGroup, GetUserGroupUrl, GetUserGroupUrlType, GetUserPrefersGroupUrl
{
    private ?WP_User $user = null;

    public function __construct(
        private WpGetCurrentUser&WpGetObjectTerms&IsWpError&GetUserMeta&GetBlogDetails $wpService,
        private GetField $acfService,
        private UserConfigInterface $userConfig
    ){}

    /**
     * Set current user
     *
     * @param WP_User $user
     * @return void
     */
    public function setUser(?WP_User $user = null): ?WP_User
    {
        $currentUser = $user ?? $this->wpService->wpGetCurrentUser();
        if(is_a($currentUser, 'WP_User') && $currentUser->ID > 0) {
            return $this->user = $currentUser;
        }
        return null;
    }

    /**
     * Check if user has a specific role
     * Can also check multiple roles, returns true if any of exists for the user
     * @param  string|array  $roles Role or roles to check
     * @return boolean
     */
    public function userHasRole(string|array $roles): bool
    {
        if(!$this->user) {
            return false;
        }

        if (is_string($roles)) {
            $roles = array($roles);
        }

        if (!array_intersect($roles, $this->user->roles)) {
            return false;
        }

        return true;
    }

    /**
     * Get user group
     *
     * @return string|null
     */
    public function getUserGroup(): ?WP_Term
    {
        if(!$this->user) {
            return null;
        }

        $user = $this->user;

        $userGroup = $this->wpService->wpGetObjectTerms($user->ID, $this->userConfig->getUserGroupTaxonomyName());
        if (empty($userGroup) || $this->wpService->isWpError($userGroup)) {
            return null;
        }

        if (is_array($userGroup)) {
            $userGroup = array_shift($userGroup);
        }

        return is_a($userGroup, 'WP_Term') ? $userGroup : null;
    }

    /**
     * Get the user group URL
     *
     * @param WP_Term|null $term
     * @return string
     */
    public function getUserGroupUrl(?WP_Term $term = null): ?string
    {
        if(!$this->user) {
            return null;
        }

        // Get the user group
        $term ??= $this->getUserGroup();

        // Ensure term exists
        if (!$term) {
            return null;
        }

        // Get the selected type of link
        $typeOfLink = $this->getUserGroupUrlType($term);

        // Get the URL
        return (
            new UserGroupUrl($typeOfLink, $term, $this->acfService, $this->wpService, $this->userConfig)
        )->get();
    }

    /**
     * Get the user group URL type
     * 
     * @param WP_Term|null $term
     * 
     * @return string|null
     */
    public function getUserGroupUrlType(?WP_Term $term = null): ?string
    {
        $term ??= $this->getUserGroup();
        $termId = $this->userConfig->getUserGroupTaxonomyName() . '_' . $term->term_id;

        return $this->acfService->getField('user_group_type_of_link', $termId) ?: null;
    }

    /**
     * Get the user prefers group URL.
     * This indicates that the user prefers to be 
     * redirected to the group URL after login.
     *
     * @return bool
     */
    public function getUserPrefersGroupUrl(): bool
    {
        if(!$this->user) {
            return false;
        }

        $perfersGroupUrl = $this->wpService->getUserMeta(
            $this->user->ID, 
            $this->userConfig->getUserPrefersGroupUrlMetaKey(), 
            true
        );
        if ($perfersGroupUrl) {
            return true;
        }
        return false;
    }
}
