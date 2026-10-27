<?php

namespace Municipio\Helper;

class IsCurrentUrl
{
    /**
     * Check if the url corresponds with current url
     *
     * @param string $url
     * @return bool
     */
    public static function isCurrentUrl(string $url): bool
    {
        $currentUrl = self::sanitizePath(self::getRequestUri());

        //Check if urls match
        if (self::parseUrlSafe($url, PHP_URL_PATH) !== null) {
            $checkUrl = self::sanitizePath(self::parseUrlSafe($url, PHP_URL_PATH));
            if ($urlQuery = self::parseUrlSafe($url, PHP_URL_QUERY)) {
                $checkUrl .=  '?' . $urlQuery;
            }

            if ($currentUrl == $checkUrl) {
                return true;
            }
        }

        //Check if querystrings match, path is empty
        if (self::parseUrlSafe($url, PHP_URL_PATH) == null && !empty(self::parseUrlSafe($url, PHP_URL_QUERY))) {
            if (self::parseUrlSafe($url, PHP_URL_QUERY) == trim(strstr($currentUrl, "?"), "?")) {
                return true;
            }
        }

        return false;
    }

    public static function isCurrentOrAncestorUrl(string $url): bool
    {
        $requestUri   = self::getRequestUri();
        $currentPath  = self::sanitizePath((string) parse_url($requestUri, PHP_URL_PATH));
        $itemPath     = self::sanitizePath(self::parseUrlSafe($url, PHP_URL_PATH));
        $itemQuery    = self::parseUrlSafe($url, PHP_URL_QUERY);

        if (empty($itemPath)) {
            return false;
        }

        // Om menylänken har query-parametrar måste de finnas i aktuell URL
        if (!empty($itemQuery)) {
            parse_str($itemQuery, $itemParams);
            parse_str((string) parse_url($requestUri, PHP_URL_QUERY), $currentParams);

            if ($currentPath !== $itemPath) {
                return false;
            }
            foreach ($itemParams as $key => $value) {
                if (!isset($currentParams[$key]) || $currentParams[$key] != $value) {
                    return false;
                }
            }
            return true;
        }

        if (strpos($currentPath, $itemPath) === 0) {
            $remaining = substr($currentPath, strlen($itemPath));
            return $remaining === '' || $remaining[0] === '/';
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

    /**
     * Get the current request uri, if it exists.
     *
     * @return string
     */
    public static function getRequestUri(): string
    {
        return isset($_SERVER['REQUEST_URI']) && is_string($_SERVER['REQUEST_URI'])
        ? $_SERVER['REQUEST_URI']
        : '';
    }

    /**
     * Get the current request uri query, if it exists.
     *
     * @return string
     */
    public static function getRequestUriQuery(): string
    {
        $requestUri = self::getRequestUri();
        if (!empty($requestUri) && strpos($requestUri, '?') !== false) {
            return self::parseUrlSafe($requestUri, PHP_URL_QUERY);
        }
        return '';
    }

    /**
     * Get the current request uri path, if it exists.
     *
     * @return string
     */
    public function getRequestUriPath(): string
    {
        $requestUri = self::getRequestUri();
        if (!empty($requestUri)) {
            return self::parseUrlSafe($requestUri, PHP_URL_PATH);
        }
        return '';
    }

    /**
     * Get the current request uri path, if it exists.
     *
     * @param string $url           The URL to parse.
     * @param int|string $method    The component to retrieve. Can be either a PHP_URL_* constant or a string with the name of the component (e.g. 'path', 'query', 'fragment', 'host', 'port', 'user', 'pass', 'scheme', 'full').
     *
     * @return string
     */
    private static function parseUrlSafe(string $url, int|string $method): string
    {
        // Validate the URL
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return '';
        }

        // Parse the URL and get the desired component
        $parsedUrl = parse_url($url, is_int($method) ? $method : constant('PHP_URL_' . strtoupper($method)));

        // Ensure that the result is a string or return an empty string if it's not set
        return is_string($parsedUrl) ? $parsedUrl : '';
    }
}
