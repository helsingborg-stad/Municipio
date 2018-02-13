<?php

namespace Municipio\Theme;

class Font
{
    public function __construct()
    {
        if (defined('WEB_FONT') && WEB_FONT != "") {
            add_action('wp_head', array($this, 'renderFontJS'), 10);
            add_action('wp_head', array($this, 'renderFontVar'), 10);
            add_action('wp_head', array($this, 'renderFontLibrary'), 5);
        }
    }

    /**
     * Print current font
     * @return void
     */
    public function renderFontVar()
    {
        if (defined('WEB_FONT_DISABLE_INLINE') && WEB_FONT_DISABLE_INLINE != "") {
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
     * Print js-function in header
     * @param  string $fontFamily Font family to save
     * @return void
     */
    public function renderFontJS()
    {
        echo "
            <script>
            WebFont.load({
                google: {
                  families: ['". WEB_FONT .":300,400,400i,500,500i,700,700i,900']
                },
                timeout: 2000
              });
            </script>
        ";
    }

    /**
     * Print js-library in header
     * @param  string $fontFamily Font family to save
     * @return void
     */
    public function renderFontLibrary()
    {
        if (!defined('WEB_FONT_REMOTE') && file_exists(MUNICIPIO_PATH . '/assets/source/js/font.js')) {
            echo '<script>';
            readfile(MUNICIPIO_PATH . '/assets/source/js/font.js');
            echo '</script>';
        } else {
            wp_enqueue_script('webfont-loader', 'https://ajax.googleapis.com/ajax/libs/webfont/1.6.26/webfont.js', array(), '1.0.0', false);
        }
    }
}
