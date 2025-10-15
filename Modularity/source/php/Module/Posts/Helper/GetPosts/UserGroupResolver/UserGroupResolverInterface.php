<?php

namespace Modularity\Module\Posts\Helper\GetPosts\UserGroupResolver;

interface UserGroupResolverInterface
{
    /**
     * Get the user group slug for the current user.
     *
     * @return string|null The user group slug or null if not found.
     */
    public function getUserGroup(): ?string;
}