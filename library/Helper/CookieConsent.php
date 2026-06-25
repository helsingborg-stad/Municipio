<?php
namespace Municipio\Helper;

class CookieConsent 
{
    private static bool|array $cookieConsent;

    /**
     * Get the cookie consents from the Pressidium plugin.
     *
     * @return array|false The consents or false if not set.
     */
    public static function getConsents() 
    {
        if (self::pressidiumIsEnabled()) {
            $consents = isset($_COOKIE['pressidium_cookie_consent']) 
                ? json_decode(stripslashes($_COOKIE['pressidium_cookie_consent']), true)
                : [];
            return self::parsePressidiumConsents($consents);
        }

        return false;
    }

    /**
     * Check if the required consents are present.
     *
     * @param array $requiredConsents The required consents.
     * @return bool True if all required consents are present, false otherwise.
     */
    public static function hasConsents(array $requiredConsents)
    {
        if (!isset(self::$cookieConsent)) {
            self::$cookieConsent = self::getConsents();
        }

        if (self::$cookieConsent === false) {
            return true;
        }

        foreach ($requiredConsents as $type) {
            if (!isset(self::$cookieConsent[$type]) || !self::$cookieConsent[$type]) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if the Pressidium cookie consent plugin is enabled.
     *
     * @return bool True if the plugin is enabled, false otherwise.
     */
    public static function pressidiumIsEnabled()
    {
        return class_exists('Pressidium\WP\CookieConsent\Plugin') 
                && is_plugin_active('pressidium-cookie-consent/pressidium-cookie-consent.php');
    }

    /**
     * Parse the Pressidium cookie consent data.
     *
     * @param array $data The cookie consent data.
     * @return array The parsed consents.
     */
    private static function parsePressidiumConsents($data) 
    {
        $consents = [
            'necessary' => false,
            'preferences' => false,
            'analytics' => false,
            'targeting' => false,
        ];

        foreach ($data['categories'] as $type) {
             $consents[$type] = true;
        }

        return $consents;
    }
}