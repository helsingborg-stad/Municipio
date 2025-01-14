<?php

namespace Municipio\Admin\Private\Config;

interface UserGroupRestrictionConfigInterface
{
    /**
     * Get the user group visibility meta key.
     *
     * @return string
     */
    public function getUserGroupVisibilityMetaKey(): string;
}
