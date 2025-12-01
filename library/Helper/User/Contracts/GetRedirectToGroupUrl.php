<?php

namespace Municipio\Helper\User\Contracts;

interface GetRedirectToGroupUrl
{
    /**
     * Get redirect to group url
     *
     * @param string $redirectTo
     * @param string $request
     * @param WP_User $userInHook
     *
     * @return string
     */
    public function getRedirectToGroupUrl(null|\WP_User|int $user = null): ?string;
}
