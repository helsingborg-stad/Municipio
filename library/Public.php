<?php

/**
 * Get a posts featured image thumbnail by post id
 * @param  int|null $post_id Post id or null
 * @return string            Thumbnail url
 */
if (!function_exists('municipio_get_thumbnail_source')) {
    function municipio_get_thumbnail_source($post_id = null)
    {
        $id = get_post_thumbnail_id($post_id);
        $src = wp_get_attachment_image_srcset($id, 'medium', true);

        if (!$src) {
            $src = wp_get_attachment_url($id);
            $src = $src;
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
    function municipio_get_logotype($type = 'standard', $tooltip = false)
    {
        $siteName = apply_filters('Municipio/logotype_text', get_bloginfo('name'));

        $logotype = array(
            'standard' => get_field('logotype', 'option'),
            'negative' => get_field('logotype_negative', 'option')
        );

        // Get the symbol to use (blog name or image)
        $symbol = '<h1 class="no-margin">' . $siteName . '</h1>';
        if (isset($logotype[$type]['url'])) {
            $symbol = sprintf(
                '<img src="%s" alt="%s">',
                $logotype[$type]['url'],
                $siteName
            );
        }

        $classes = apply_filters('Municipio/logotype_class', array('logotype'));
        $tooltip = apply_filters('Municipio/logotype_tooltip', $tooltip);

        // Build the markup
        $markup = sprintf(
            '<a href="%s" class="%s" %s>%s</a>',
            home_url(),
            implode(' ', $classes),
            ($tooltip !== false && !empty($tooltip)) ? 'data-tooltip="' . $tooltip . '"' : '',
            $symbol
        );

        return $markup;
    }
}
