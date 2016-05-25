<?php

if (!function_exists('municipio_intranet_format_site_name')) {
    /**
     * Get profile url
     * @param  mixed $user User id or login name, default is current logged in user
     * @return string
     */
    function municipio_intranet_format_site_name($site) {
        return ($site['short_name']) ? $site['short_name'] . ' <em>' . $site['name'] . '</em>' : $site['name'];
    }
}
