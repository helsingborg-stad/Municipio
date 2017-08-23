<?php

/**
 * Gets the html markup for the logotype
 * @param  string  $type    Logotype source
 * @param  boolean $tooltip Show tooltip or not
 * @return string           HTML markup
 */
if (!function_exists('municipio_intranet_get_logotype')) {
    function municipio_intranet_get_logotype($type = 'standard', $logo_include = true, $tagline = false)
    {
        if ($type == '') {
            $type = 'standard';
        }

        switch_to_blog(BLOG_ID_CURRENT_SITE);

        $siteName = apply_filters('Municipio/logotype_text', get_bloginfo('name'));
        $tooltip = get_field('logotype_tooltip', 'option');
        $tagline = get_field('header_tagline_enable', 'option');

        $logotype = array(
            'standard' => get_field('logotype', 'option'),
            'negative' => get_field('logotype_negative', 'option')
        );

        // Get the symbol to use (blog name or image)
        $symbol = '<span class="h1 no-margin no-padding">' . $siteName . '</span>';

        if (isset($logotype[$type]['url']) && $logo_include === false) {
            $symbol = sprintf(
                '<img src="%s" alt="%s">',
                $logotype[$type]['url'],
                $siteName
            );
        }

        // Get the symbol to use (by file include)
        if (isset($logotype[$type]['id']) && $logo_include === true) {
            $symbol = \Municipio\Helper\Svg::extract(get_attached_file($logotype[$type]['id']));
        }

        $classes = apply_filters('Municipio/logotype_class', array('logotype'));
        $tooltip = apply_filters('Municipio/logotype_tooltip', $tooltip);
        $taglineHtml = '';

        if ($tagline === true) {
            $taglineText = get_bloginfo('description');

            if (get_field('header_tagline_type', 'option') == 'custom') {
                $taglineText = get_field('header_tagline_text', 'option');
            }

            $taglineHtml = '<span class="tagline">' . $taglineText . '</span>';
        }

        // Build the markup
        $markup = sprintf(
            '<a href="%s" class="%s" %s>%s%s</a>',
            home_url(),
            implode(' ', $classes),
            ($tooltip !== false && !empty($tooltip)) ? 'data-tooltip="' . $tooltip . '"' : '',
            $symbol,
            $taglineHtml
        );

        restore_current_blog();

        return $markup;
    }
}

if (!function_exists('municipio_intranet_format_site_name')) {
    /**
     * Formats a site's name correctly from a site array
     * @param  array $site The site to format name for
     * @return string
     */
    function municipio_intranet_format_site_name($site, $parts = 'all')
    {

        if (!is_object($site)) {
            $site = \Intranet\Helper\Multisite::getSite(get_current_blog_id());
        }

        switch ($parts) {
            case 'short':
                return ($site->short_name) ? $site->short_name : '';

            case 'long':
                return $site->name;

            default:
                return ($site->short_name) ? $site->short_name . ' <em>' . $site->name . '</em>' : $site->name;
        }

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

if (!function_exists('municipio_intranet_current_url')) {
    /**
     * Gets the current url
     * @return string
     */
    function municipio_intranet_current_url()
    {
        return "//{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
    }
}
