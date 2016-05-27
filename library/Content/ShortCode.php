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
    }

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
}
