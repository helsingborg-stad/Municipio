<?php

namespace Municipio\Theme;

class Font
{
    public function __construct()
    {
        add_action('wp_head', array($this, 'renderInlineFontCss'), 10);
        add_action('wp_head', array($this, 'enqueueFontLoad'), 10);
    }

    /**
     * Print current font inline in head
     * 
     * @return void
     */
    public function renderInlineFontCss()
    {
        //Disable inline print
        if (defined('WEB_FONT_DISABLE_INLINE') && WEB_FONT_DISABLE_INLINE != "") {
            return;
        }

        //Disable font if not defined. 
        if (!defined('WEB_FONT') || (defined('WEB_FONT') && WEB_FONT == "")) { 
            return;
        }

        echo "
            <style>
                body {
                    font-family: " . WEB_FONT . ",system,Segoe UI,Tahoma,-apple-system;
                }
            </style>
        ";

    }

    /**
     * Enqueue webfont from google web fonts with fontswapping
     *
     * @return void
     */
    public function enqueueFontLoad() {

        //Disable font if not defined. 
        if (!defined('WEB_FONT') || (defined('WEB_FONT') && WEB_FONT == "")) { 
            return;
        }

        wp_enqueue_style('municipio-font', 'https://fonts.googleapis.com/css2?family=' . WEB_FONT . ':ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap'); 

    }
}
