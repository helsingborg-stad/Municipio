<?php

namespace Municipio\Admin\Private\Config;

/**
 * User group restriction config
 */
class UserGroupRestrictionConfig implements UserGroupRestrictionConfigInterface
{
    /**
     * Get the user group visibility meta key.
     *
     * @return string
     */
    public function getUserGroupVisibilityMetaKey(): string
    {
        return 'user-group-visibility';
    }
}
