<?php

if (!function_exists('municipio_show_posts_pag')) {
    function municipio_show_posts_pag()
    {
        global $wp_query;
        return ($wp_query->max_num_pages > 1);
    }
}

/**
 * Get a posts featured image thumbnail by post id
 * @param  int|null $post_id Post id or null
 * @return string            Thumbnail url
 */
if (!function_exists('municipio_get_thumbnail_source')) {
    function municipio_get_thumbnail_source($post_id = null, $size = array())
    {

        //Use current id as default
        if (is_null($post_id)) {
            $post_id = get_the_id();
        }

        //Get post thumbnail id (Default value for src)
        $thumbnail_id   = get_post_thumbnail_id($post_id);
        $src            = false;

        //Get default vale
        if (isset($size[0]) && isset($size[1])) {
            $src = wp_get_attachment_image_src(
                $thumbnail_id,
                municipio_to_aspect_ratio('16:9', array($size[0], $size[1]))
            );
        } else {
            $src = wp_get_attachment_image_src($thumbnail_id, 'medium');
        }

        //Get url from array
        $src = isset($src[0]) ? $src[0] : false;

        //Force get attachment url (full size)
        if (!$src) {
            $src = wp_get_attachment_url($thumbnail_id);
        }

        return $src;
    }
}

/**
 * Gets the html markup for the logotype
 * @param  string  $type    Logotype source
 * @param  boolean $tooltip Show tooltip or not
 * @return string           HTML markup
 */
if (!function_exists('municipio_get_logotype')) {
    function municipio_get_logotype($type = 'standard', $tooltip = false, $logo_include = true, $tagline = false)
    {
        if ($type == '') {
            $type = 'standard';
        }

        $siteName = apply_filters('Municipio/logotype_text', get_bloginfo('name'));

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

        return $markup;
    }
}

if (!function_exists('municipio_human_datediff')) {
    function municipio_human_datediff($date)
    {
        $diff = human_time_diff(strtotime($date), current_time('timestamp'));
        return $diff;
    }
}

if (!function_exists('municipio_get_mime_link_item')) {
    function municipio_get_mime_link_item($mime)
    {
        $mime = explode('/', $mime);

        if (!isset($mime[0])) {
            return '';
        }

        return 'link-item link-item-' . $mime[0];
    }
}

if (!function_exists('municipio_to_aspect_ratio')) {
    function municipio_to_aspect_ratio($ratio, $size)
    {
        if (count($ratio = explode(":", $ratio)) == 2) {
            $width  = round($size[0]);
            $height = round(($width / $ratio[0]) * $ratio[1]);
        }
        return array($width, $height);
    }
}

if (!function_exists('municiipio_format_currency')) {
    function municiipio_format_currency($value)
    {
        $value = str_split(strrev($value), 3);
        $value = strrev(implode(" ", $value));
        return $value;
    }
}

if (!function_exists('municipio_get_author_full_name')) {
    /**
     * Get url to manage subscriptions page
     * @param  mixed $user User id or login name, default is current logged in user
     * @return string
     */
    function municipio_get_author_full_name($author = null)
    {
        if (is_null($author)) {
            $author = get_the_author_meta('ID');
        }

        if (!empty(get_user_meta($author, 'first_name', true)) && !empty(get_user_meta($author, 'last_name', true))) {
            return get_user_meta($author, 'first_name', true) . ' ' . get_user_meta($author, 'last_name', true);
        }

        return get_user_meta($author, 'nicename', true);
    }
}

if (!function_exists('municipio_post_taxonomies_to_display')) {
    /**
     * Gets "public" (set via theme options) taxonomies and terms for a specific post
     * @param  int    $postId The id of the post
     * @return array          Taxs and terms
     */
    function municipio_post_taxonomies_to_display(int $postId) : array
    {
        $taxonomies = array();
        $post = get_post($postId);
        $taxonomiesToShow = get_field('archive_' . sanitize_title($post->post_type) . '_post_taxonomy_display', 'option');

        foreach ((array)$taxonomiesToShow as $taxonomy) {
            $taxonomies[$taxonomy] = apply_filters('Municipio/taxonomies_to_display/terms', wp_get_post_terms($postId, $taxonomy), $postId, $taxonomy);
        }

        $taxonomies = array_filter($taxonomies);

        return $taxonomies;
    }
}

if (!function_exists('municipio_current_url')) {
    /**
     * Gets the current url
     * @return string
     */
    function municipio_intranet_current_url()
    {
        return "//{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
    }
}
