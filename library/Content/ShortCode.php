<?php

namespace Municipio\Content;

class ShortCode
{
    public function __construct()
    {
        add_action('init', array($this, 'registerShortCode'));
    }

    public function registerShortCode()
    {
        add_shortcode('meta', array($this,'displayMetaValue'));
        add_shortcode('explain', array($this, 'explain'));
        add_shortcode('pricon', array($this, 'pricon'));
    }

    /**
     * Insert icons with shortcode
     * Example: [pricon icon="dog" color="#ff0000" size="5"]
     * @param  array $atts     Shortcode attributes
     * @param  string $content
     * @return string          Rendered shortcode html
     */
    public function pricon($atts, $content = '')
    {
        if (!isset($atts['icon'])) {
            return '';
        }

        $classes = array(
            'pricon',
            'pricon-' . $atts['icon']
        );

        if (isset($atts['size'])) {
            if (is_numeric($atts['size'])) {
                $classes[] = 'pricon-' . $atts['size'] . 'x';
            } else {
                $classes[] = 'pricon-' . $atts['size'];
            }
        }

        $html = '<i class="' . implode(' ', $classes) . '"';

        if (isset($atts['color']) && !empty($atts['color'])) {
            $html .= ' style="color: ' . $atts['color'] . ';"';
        }

        $html .= '></i>';

        return $html;
    }

    /**
     * Shortcode function for "meta"
     * @param  array $atts     Attributes
     * @param  string $content The content of the shortcode
     * @return mixed
     */
    public function displayMetaValue($atts, $content = "")
    {
        //Default value
        extract(shortcode_atts(array(
            'key' => ''
        ), $atts));

        //Get field with formatting if exits
        if (function_exists('get_field')) {
            return (string) get_field($key);
        } else {
            global $post;
            return (string) get_post_meta($post->ID, $key);
        }
    }

    public function explain($atts, $content = '')
    {
        extract(shortcode_atts(array(
            'tooltip' => ''
        ), $atts));

        return '<span class="explain"><em>' . $content . '</em> <span data-tooltip="' . $tooltip . '"><i class="pricon pricon-question-o"></i></span></span>';
    }
}
