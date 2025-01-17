<?php

namespace Municipio\Helper\User;

use AcfService\Contracts\GetField;
use Municipio\Helper\Term\Contracts\CreateOrGetTermIdFromString;
use Municipio\Helper\User\Config\UserConfigInterface;
use Municipio\Helper\User\Contracts\{UserHasRole, GetUserGroup, GetUserGroupUrl, GetUserGroupUrlType, GetUserPrefersGroupUrl, GetUser, SetUserGroup};
use Municipio\Helper\User\FieldResolver\UserGroupUrl;
use Municipio\UserGroup\Config\UserGroupConfigInterface;
use WP_Term;
use WP_User;
use WpService\WpService;

/**
 * User helper.
 */
class User implements UserHasRole, GetUserGroup, GetUserGroupUrl, GetUserGroupUrlType, GetUserPrefersGroupUrl, GetUser, SetUserGroup
{
    /**
     * Constructor.
     */
    public function __construct(
        private WpService $wpService,
        private GetField $acfService,
        private UserConfigInterface $userConfig,
        private UserGroupConfigInterface $userGroupConfig,
        private CreateOrGetTermIdFromString $termHelper
    ) {
    }

    /**
     * @inheritDoc
     */
    public function getUser(null|WP_User|int $user = null): ?WP_User
    {
        if (is_a($user, 'WP_User') && $user->ID > 0) {
            return $user;
        }

        if (is_int($user) && $user > 0) {
            $retrievedUser = $this->wpService->getUserBy('ID', $user);

            if (is_a($retrievedUser, 'WP_User')) {
                return $retrievedUser;
            }
        }

        if (is_null($user)) {
            $retrievedUser = $this->wpService->wpGetCurrentUser();

            if (is_a($retrievedUser, 'WP_User') && $retrievedUser->ID > 0) {
                return $retrievedUser;
            }
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    public function userHasRole(string|array $roles, null|WP_User|int $user = null): bool
    {
        $user = $this->getUser($user);

        if (!$user) {
            return false;
        }

        if (is_string($roles)) {
            $roles = array($roles);
        }

        if (!array_intersect($roles, $user->roles)) {
            return false;
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function getUserGroup(null|\WP_User|int $user = null): ?WP_Term
    {
        $user = $this->getUser($user);

        if (!$user) {
            return null;
        }

        $this->maybeSwitchToMainSite();
        $userGroup = $this->wpService->wpGetObjectTerms($user->ID, $this->userGroupConfig->getUserGroupTaxonomy());
        $this->switchToCurrentBlogIfSwitched();

        if (empty($userGroup) || $this->wpService->isWpError($userGroup)) {
            return null;
        }

        if (is_array($userGroup)) {
            $userGroup = array_shift($userGroup);
        }

        return is_a($userGroup, 'WP_Term') ? $userGroup : null;
    }

    /**
     * @inheritDoc
     */
    public function getUserGroupUrl(?WP_Term $term = null, null|WP_User|int $user = null): ?string
    {
        $user = $this->getUser($user);

        if (!$user) {
            return null;
        }

        // Get the user group
        $term ??= $this->getUserGroup($user);

        // Ensure term exists
        if (!$term) {
            return null;
        }

        // Get the selected type of link
        $typeOfLink = $this->getUserGroupUrlType($term, $user);

        // Get the URL
        return (
            new UserGroupUrl($typeOfLink, $term, $this->acfService, $this->wpService, $this->userConfig, $this->userGroupConfig)
        )->get();
    }

    /**
     * @inheritDoc
     */
    public function getUserGroupUrlType(?WP_Term $term = null, null|WP_User|int $user = null): ?string
    {
        $term ??= $this->getUserGroup($user);
        $termId = $this->userGroupConfig->getUserGroupTaxonomy($user) . '_' . $term->term_id;

        $this->maybeSwitchToMainSite();
        return $this->acfService->getField('user_group_type_of_link', $termId) ?: null;
        $this->switchToCurrentBlogIfSwitched();
    }

    /**
     * @inheritDoc
     */
    public function getUserPrefersGroupUrl(null|WP_User|int $user = null): ?bool
    {
        $user = $this->getUser($user);

        if (!$user) {
            return null;
        }

        $this->maybeSwitchToMainSite();
        $perfersGroupUrl = $this->wpService->getUserMeta($user->ID, $this->userConfig->getUserPrefersGroupUrlMetaKey(), true);
        $this->switchToCurrentBlogIfSwitched();

        if ($perfersGroupUrl) {
            return true;
        }
        return false;
    }

/**
     * Switch to main site if multisite and not on main site.
     */
    private function maybeSwitchToMainSite(): void
    {
        if (!$this->wpService->isMultisite() || $this->wpService->isMainSite()) {
            return;
        }

        if ($this->wpService->getMainSiteId() === $this->wpService->getCurrentBlogId()) {
            return;
        }

        $this->wpService->switchToBlog($this->wpService->getMainSiteId());
    }

    /**
     * Switch back from main site if multisite and switched.
     */
    private function switchToCurrentBlogIfSwitched(): void
    {
        if (!$this->wpService->isMultisite() || !$this->wpService->msIsSwitched()) {
            return;
        }

        $this->wpService->restoreCurrentBlog();
    }

    /**
     * @inheritDoc
     */
    public function setUserGroup(string $groupName, null|WP_User|int $user = null): void
    {
        $user = $this->getUser($user);

        if (!$user) {
            return;
        }

        $taxonomy = $this->userGroupConfig->getUserGroupTaxonomy();

        $this->maybeSwitchToMainSite();

        if ($termId = $this->termHelper->createOrGetTermIdFromString($groupName, $taxonomy)) {
            $this->wpService->wpSetObjectTerms($user->ID, $termId, $taxonomy, false);
        }

        $this->switchToCurrentBlogIfSwitched();
    }
}
