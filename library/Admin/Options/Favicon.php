<?php

namespace Municipio\Admin\Options;

class Favicon
{
    public function __construct()
    {
        add_filter('acf/load_field/name=favicon_type', array($this, 'loadFaviconTypeAlternatives'), 10, 3);
        add_action('wp_head', array($this, 'addFavIconsToHead'));
    }

    public function addFavIconsToHead()
    {
        $icons = get_field('favicons', 'option');

        foreach ($icons as $icon) {
            $tag = $this->getTag($icon);
            echo apply_filters('Municipio/favicon_tag', $tag, $icon) . "\n";
        }
    }

    public function getTag($icon)
    {
        switch ($icon['favicon_type']) {
            case '152':
                return '<link rel="apple-touch-icon-precomposed" href="' . $icon['favicon_icon']['url'] . '">';
                break;

            case '144':
                return '<meta name="msapplication-TileColor" content="' . $icon['favicon_tile_color'] . '">' . "\n" .
                       '<meta name="msapplication-TileImage" content="' . $icon['favicon_icon']['url'] . '">';
                break;

            case 'fav':
                return '<link rel="icon" href="' . $icon['favicon_icon']['url'] . '" type="image/x-icon">';
                break;

            default:
                return '';
                break;
        }

        return '';
    }

    public function loadFaviconTypeAlternatives($field)
    {
        $faviconSizes = apply_filters('Municipio/favicon_sizes', array(
            'fav' => 'favicon.ico (16x16px, 32x32px, 48x48px)',
            '152' => 'iOS, Android (152x152px)',
            '144' => 'IE10, Windows Metro (144x144px)'
        ));

        $field['choices'] = $faviconSizes;
        return $field;
    }
}
