<?php

namespace Municipio\UserGroup\Config;

use WpService\Contracts\GetMainSiteId;
use WpService\Contracts\IsMultisite;

/**
 * User group feature configuration.
 */
class UserGroupConfig implements UserGroupConfigInterface
{
    /**
     * Constructor.
     */
    public function __construct(
        private IsMultisite&GetMainSiteId $wpService
    ) {
    }

    /**
     * Check if the MiniOrange plugin is enabled.
     *
     * @return bool
     */
    public function isEnabled(): bool
    {
        return true;
    }

    /**
     * Get the user group taxonomy.
     * This is the name of the taxonomy that
     * will be used to store the user groups ie "company name" in most idp implementations.
     *
     * @return string
     */
    public function getUserGroupTaxonomy(): string
    {
        return 'user_group';
    }


    /**
     * Get if the user group taxonomy only should be used/stored on the main blog.
     *
     * @return bool|int     Integer that represents the main site id or false if not multisite.
     */
    public function getStoreUserGroupTaxonomyOnMainBlog(): int|false
    {
        if ($this->wpService->isMultisite() && $mainSiteId = $this->wpService->getMainSiteId()) {
            return $mainSiteId;
        }
        return false;
    }
}
