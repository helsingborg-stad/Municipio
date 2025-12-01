<?php

namespace Municipio\UserGroup\Config;

interface UserGroupConfigInterface
{
    /**
     * Is feature enabled.
     *
     * @return bool
     */
    public function isEnabled(): bool;

    /**
     * Get the name of the user group taxonomy.
     *
     * @return string
     */
    public function getUserGroupTaxonomy(): string;
}
