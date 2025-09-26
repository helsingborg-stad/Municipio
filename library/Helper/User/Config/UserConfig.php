<?php

namespace Municipio\Helper\User\Config;

/**
 * Class UserConfig
 */
class UserConfig implements UserConfigInterface
{
    /**
     * Get the meta key for user prefers group URL setting.
     *
     * @return string
     */
    public function getUserPrefersGroupUrlMetaKey(): string
    {
        return 'user_prefers_group_url';
    }

    /**
     * Get the default role for new users.
     *
     * @return string
     */
    public function getDefaultRole(): string
    {
        return 'subscriber';
    }
}
