<?php

namespace Municipio\Controller\Navigation\Helper;

use Municipio\Helper\WpService;

class IsUserLoggedIn
{
    /**
     * Check if the user is logged in.
     *
     * This method checks if the user is logged in by utilizing the WordPress function `is_user_logged_in()`.
     * It caches the result to avoid multiple calls during the same request.
     *
     * @return bool True if the user is logged in, false otherwise.
     */
    public static function isUserLoggedIn(): bool
    {
        static $isUserLoggedIn = null;

        if ($isUserLoggedIn === null) {
            $isUserLoggedIn = WpService::get()->isUserLoggedIn();
        }

        return $isUserLoggedIn;
    }
}
