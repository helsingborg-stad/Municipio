<?php

if (!function_exists('municipio_intranet_format_site_name')) {
    /**
     * Formats a site's name correctly from a site array
     * @param  array $site The site to format name for
     * @return string
     */
    function municipio_intranet_format_site_name($site)
    {
        return ($site['short_name']) ? $site['short_name'] . ' <em>' . $site['name'] . '</em>' : $site['name'];
    }
}

if (!function_exists('municipio_table_of_contents_url')) {
    /**
     * Retursn the table of contents link url
     * @return string Url
     */
    function municipio_table_of_contents_url()
    {
        global $current_site;

        $url = network_home_url('table-of-contents');

        if (get_current_blog_id() !== $current_site->id) {
            $url .= '?department=' . get_current_blog_id();
        }

        return $url;
    }
}
