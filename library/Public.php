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
    function municipio_get_logotype($type = 'standard', $tooltip = false, $logo_include = true)
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
        $symbol = '<h1 class="no-margin">' . $siteName . '</h1>';

        if (isset($logotype[$type]['url']) && $logo_include === false) {
            $symbol = sprintf(
                '<img src="%s" alt="%s">',
                $logotype[$type]['url'],
                $siteName
            );
        }

        // Get the symbol to use (by file include)
        if (isset($logotype[$type]['id']) && $logo_include === true) {

            //Get file contents
            $symbol = file_get_contents(
                get_attached_file($logotype[$type]['id'])
            );

            //Get by dom method
            if (class_exists('DOMDocument')) {
                $doc = new DOMDocument();
                if ($doc->loadXML($symbol) === true) {
                    try {
                        $doc->getElementsByTagName('svg');

                        $svg = $doc->getElementsByTagName('svg');
                        if ($svg->item(0)->C14N() !== null) {
                            $symbol = $svg->item(0)->C14N();
                        }
                    } catch (exception $e) {
                        error_log("Error loading SVG file to header or footer.");
                    }
                }
            }

            //Filter tags & comments (if above not applicated)
            $symbol = preg_replace('/<\?xml.*?\/>/im', '', $symbol); //Remove XML
            $symbol = preg_replace('/<!--(.*)-->/Uis', '', $symbol); //Remove comments & javascript
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

if (!function_exists('municipio_human_datediff')) {
    function municipio_human_datediff($date)
    {
        $diff = human_time_diff(strtotime($date), current_time('timestamp'));
        return $diff;
    }
}
