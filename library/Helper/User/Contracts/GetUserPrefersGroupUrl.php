<?php

namespace Municipio\Helper\User\Contracts;

interface GetUserPrefersGroupUrl
{
    /**
     * Get user prefers group URL.
     *
     * @param null|\WP_User|int $user User to get prefers group URL for. Defaults to current user.
     *
     * @return bool|null User prefers group URL. Null if user not found.
     */
    public function getUserPrefersGroupUrl(null|\WP_User|int $user = null): ?bool;
}
