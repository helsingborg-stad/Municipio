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

        return '<span class="explain">
            <em>' . $content . '</em>
            <span data-tooltip="' . $tooltip . '"><i class="fa fa-question-circle"></i></span>
        </span>';
    }
}
