<?php

namespace Municipio\Helper\User;

use AcfService\Contracts\GetField;
use Municipio\Helper\Term\Contracts\CreateOrGetTermIdFromString;
use Municipio\Helper\User\Config\UserConfigInterface;
use Municipio\Helper\User\Contracts\{GetRedirectToGroupUrl, UserHasRole, GetUserGroup, GetUserGroupUrl, GetUserGroupUrlType, GetUserPrefersGroupUrl, GetUser, SetUserGroup};
use Municipio\Helper\User\FieldResolver\UserGroupUrl;
use Municipio\Helper\SiteSwitcher\SiteSwitcher;
use Municipio\UserGroup\Config\UserGroupConfigInterface;
use Municipio\UserGroup\CreateUserGroupTaxonomy;
use WP_Term;
use WP_User;
use WpService\WpService;

/**
 * User helper.
 */
class User implements
    UserHasRole,
    GetUserGroup,
    GetUserGroupUrl,
    GetUserGroupUrlType,
    GetUserPrefersGroupUrl,
    GetUser,
    GetRedirectToGroupUrl,
    SetUserGroup
{
    /**
     * Constructor.
     */
    public function __construct(
        private WpService $wpService,
        private GetField $acfService,
        private UserConfigInterface $userConfig,
        private UserGroupConfigInterface $userGroupConfig,
        private CreateOrGetTermIdFromString $termHelper,
        private SiteSwitcher $siteSwitcher
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

        if (!$this->wpService->isMultisite()) {
            return null;
        }

        $userGroup = $this->siteSwitcher->runInSite(
            $this->wpService->getMainSiteId(),
            function () use ($user) {

                (new \Municipio\UserGroup\CreateUserGroupTaxonomy(
                    $this->wpService,
                    $this->userGroupConfig,
                    $this->siteSwitcher
                ))->registerUserGroupTaxonomy();

                return $this->wpService->wpGetObjectTerms(
                    $user->ID,
                    $this->userGroupConfig->getUserGroupTaxonomy()
                );
            }
        );

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

        if (!$typeOfLink) {
            return null;
        }

        // Initialize the URL resolver
        $urlResolver = new UserGroupUrl(
            $typeOfLink,
            $term,
            $this->acfService,
            $this->wpService,
            $this->userConfig,
            $this->userGroupConfig
        );

        // Resolve the URL
        $resolvedUrl = $this->siteSwitcher->runInSite(
            $this->wpService->getMainSiteId(),
            function () use ($urlResolver) {
                return $urlResolver->get();
            }
        );

        return $resolvedUrl;
    }

    /**
     * @inheritDoc
     */
    public function getUserGroupUrlType(?WP_Term $term = null, null|WP_User|int $user = null): ?string
    {
        $term ??= $this->getUserGroup($user);
        $termId = $this->userGroupConfig->getUserGroupTaxonomy($user) . '_' . $term->term_id;

        $userGroupUrlType = $this->siteSwitcher->runInSite(
            $this->wpService->getMainSiteId(),
            function () use ($termId) {
                return $this->acfService->getField('user_group_type_of_link', $termId) ?: null;
            }
        );

        return $userGroupUrlType;
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

        if (!$this->wpService->isMultisite()) {
            return null;
        }

        $perfersGroupUrl = $this->siteSwitcher->runInSite(
            $this->wpService->getMainSiteId(),
            function () use ($user) {
                return $this->wpService->getUserMeta(
                    $user->ID,
                    $this->userConfig->getUserPrefersGroupUrlMetaKey(),
                    true
                );
            }
        );

        if ($perfersGroupUrl) {
            return true;
        }
        return false;
    }

    /**
     * @inheritDoc
     */
    public function getRedirectToGroupUrl(null|WP_User|int $user = null): ?string
    {
        $user = $this->getUser($user);

        if (!$user) {
            return null;
        }

        $perfersGroupUrl = $this->getUserPrefersGroupUrl($user);
        $groupUrl        = $this->getUserGroupUrl(null, $user);

        if ($perfersGroupUrl && $groupUrl) {
            return $this->wpService->addQueryArg([
                'loggedin'     => 'true',
                'prefersgroup' => 'true'
            ], $groupUrl);
        }

        return null;
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

        $this->siteSwitcher->runInSite(
            $this->wpService->getMainSiteId(),
            function () use ($groupName, $taxonomy, $user) {
                if ($termId = $this->termHelper->createOrGetTermIdFromString($groupName, $taxonomy)) {
                    (new CreateUserGroupTaxonomy(
                        $this->wpService,
                        $this->userGroupConfig,
                        $this->siteSwitcher
                    ))->registerUserGroupTaxonomy();

                    $this->wpService->wpSetObjectTerms($user->ID, $termId, $taxonomy, false);
                }
            }
        );
    }
}
