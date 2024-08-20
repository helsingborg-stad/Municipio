<?php

namespace Municipio\Helper;

class IsCurrentUrl {
    /**
     * Check if the url corresponds with current url
     *
     * @param string $url
     * @return bool
     */
    public static function isCurrentUrl(string $url): bool
    {
        $currentUrl = self::sanitizePath($_SERVER['REQUEST_URI']);

        //Check if urls match
        if (parse_url($url, PHP_URL_PATH) !== null) {
            $checkUrl = self::sanitizePath(parse_url($url, PHP_URL_PATH));
            if ($urlQuery = parse_url($url, PHP_URL_QUERY)) {
                $checkUrl .=  '?' . $urlQuery;
            }

            if ($currentUrl == $checkUrl) {
                return true;
            }
        }

        //Check if querystrings match, path is empty
        if (parse_url($url, PHP_URL_PATH) == null && !empty(parse_url($url, PHP_URL_QUERY))) {
            if (parse_url($url, PHP_URL_QUERY) == trim(strstr($currentUrl, "?"), "?")) {
                return true;
            }
        }

        return false;
    }

    /**
     * Normalize url
     *
     * @param string $path
     * @return string
     */
    public static function sanitizePath(string $path): string
    {
        return rtrim(trim($path, '/'), '/');
    }
}