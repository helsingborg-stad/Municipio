<?php

namespace Municipio\Helper;

class ReCaptcha
{
    /**
     * Check if Google reCaptcha request is valid
     * @param  string $response Google reCaptcha response
     * @return bool             If valid or not
     */
    public static function controlReCaptcha($response) : bool
    {
        $g_recaptcha_secret = defined('G_RECAPTCHA_SECRET') ? G_RECAPTCHA_SECRET : '';

        // Make a GET request to the Google reCAPTCHA server
        $request = wp_remote_get('https://www.google.com/recaptcha/api/siteverify?secret=' . $g_recaptcha_secret . '&response=' . $response . '&remoteip=' . $_SERVER["REMOTE_ADDR"]);

        // Get the request response body
        $response_body = wp_remote_retrieve_body($request);
        $result = json_decode($response_body, true);

        return $result['success'];
    }
}
