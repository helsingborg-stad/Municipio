<?php

if (!function_exists('municipio_intranet_format_site_name')) {
    /**
     * Formats a site's name correctly from a site array
     * @param  array $site The site to format name for
     * @return string
     */
    function municipio_intranet_format_site_name($site) {
        return ($site['short_name']) ? $site['short_name'] . ' <em>' . $site['name'] . '</em>' : $site['name'];
    }
}
