<?php

namespace Municipio\UserGroup\Config;

/**
 * User group feature configuration.
 */
class UserGroupConfig implements UserGroupConfigInterface
{
    /**
     * Constructor.
     */
    public function __construct()
    {
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
}
