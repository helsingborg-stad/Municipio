<?php

namespace Municipio\Helper\User;

use AcfService\Contracts\GetField;
use Municipio\Helper\Term\Contracts\CreateOrGetTermIdFromString;
use Municipio\Helper\User\Config\UserConfigInterface;
use Municipio\Helper\User\Contracts\{GetRedirectToGroupUrl, UserHasRole, GetUserGroup, GetUserGroupUrl, GetUserGroupUrlType, GetUserPrefersGroupUrl, GetUser, SetUserGroup};
use Municipio\Helper\User\FieldResolver\UserGroupUrl;
use Municipio\UserGroup\Config\UserGroupConfigInterface;
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
    // Constructor and other methods...

    /**
     * @inheritDoc
     */
    public function getUserGroup(null|\WP_User|int $user = null): ?WP_Term
    {
        $user = $this->getUser($user);

        if (!$user) {
            return null;
        }

        $userGroup = null;

        $this->runInAnotherSite($this->wpService->getMainSiteId(), function () use ($user, &$userGroup) {
            $terms = $this->wpService->wpGetObjectTerms($user->ID, $this->userGroupConfig->getUserGroupTaxonomy());
            if (!empty($terms) && !$this->wpService->isWpError($terms)) {
                $userGroup = is_array($terms) ? array_shift($terms) : $terms;
            }
        });

        return is_a($userGroup, 'WP_Term') ? $userGroup : null;
    }

    /**
     * @inheritDoc
     */
    public function getUserGroupUrlType(?WP_Term $term = null, null|WP_User|int $user = null): ?string
    {
        $term ??= $this->getUserGroup($user);

        if (!$term) {
            return null;
        }

        $typeOfLink = null;

        $this->runInAnotherSite($this->wpService->getMainSiteId(), function () use ($term, &$typeOfLink) {
            $termId = $this->userGroupConfig->getUserGroupTaxonomy() . '_' . $term->term_id;
            $typeOfLink = $this->acfService->getField('user_group_type_of_link', $termId) ?: null;
        });

        return $typeOfLink;
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

        $prefersGroupUrl = false;

        $this->runInAnotherSite($this->wpService->getMainSiteId(), function () use ($user, &$prefersGroupUrl) {
            $prefersGroupUrl = $this->wpService->getUserMeta(
                $user->ID,
                $this->userConfig->getUserPrefersGroupUrlMetaKey(),
                true
            );
        });

        return (bool) $prefersGroupUrl;
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

        $this->runInAnotherSite($this->wpService->getMainSiteId(), function () use ($groupName, $user, $taxonomy) {
            if ($termId = $this->termHelper->createOrGetTermIdFromString($groupName, $taxonomy)) {
                $this->wpService->wpSetObjectTerms($user->ID, $termId, $taxonomy, false);
            }
        });
    }

    /**
     * Run code in the context of another site.
     */
    private function runInAnotherSite(int $siteId, callable $callable): void
    {
        $this->wpService->switchToBlog($siteId);

        try {
            $callable();
        } finally {
            $this->wpService->restoreCurrentBlog();
        }
    }
}