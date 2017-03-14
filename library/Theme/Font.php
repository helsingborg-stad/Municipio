<?php

namespace Municipio\Theme;

/**
 * Set theme fonts with constant THEME_FONTS. Eg: 'Roboto,Helvetica,Arial'
 * Use web font with constant WEB_FONT
 */

class Font
{
    public $api_url = 'https://www.googleapis.com/webfonts/v1/webfonts';

    public function __construct()
    {
        if (defined('WEB_FONT')) {
            add_action('admin_init', array($this, 'checkFont'));
            add_action('wp_head', array($this, 'renderFontVar'), 5);
            add_action('wp_head', array($this, 'renderFontJS'), 5);
        }
        if (defined('THEME_FONTS')) {
            add_action('wp_head', array($this, 'addFontFamilies'), 7);
        }
    }

    public function renderFontVar()
    {
        echo '<script>';
        echo 'var webFont = '. json_encode(array(
                'fontFamily' => get_option('theme_font_family'),
                'md5'        => get_option('theme_font_md5'),
                'fontFile'   => get_option('theme_font_file'),
            )) . ';';
        echo '</script>';
    }

    public function renderFontJS()
    {
        if (file_exists(MUNICIPIO_PATH . '/assets/dist/js/font.min.js')) {
            echo '<script>';
                readfile(MUNICIPIO_PATH . '/assets/dist/js/font.min.js');
            echo '</script>';
        }
    }

    public function addFontFamilies()
    {
        echo '<style> body { font-family: ' . THEME_FONTS . ' } </style>';
    }

    public function checkFont()
    {
        if (WEB_FONT != get_option('theme_font_family')) {
            $this->saveFont(WEB_FONT);
        }
    }

    /**
     * Save new font
     * @param  string $font_family Font family to save
     * @return void
     */
    public function saveFont($font_family)
    {
        $font_file = str_replace(' ', '_', strtolower($font_family)) . '.json';
        $allowed_styles = array('regular', '600', '700', 'italic', '600italic', '700italic',);
        $md5 = '';

        if (file_exists(MUNICIPIO_PATH . 'assets/source/fonts/' . $font_file)) {
            $file_content = file_get_contents(MUNICIPIO_PATH . 'assets/source/fonts/' . $font_file);
            $file_object = json_decode($file_content);
            $md5 = $file_object->md5;
        } else {
            $url = (defined('GOOGLE_FONT_KEY')) ? $this->api_url . '?key=' . GOOGLE_FONT_KEY : null;
            $fonts_json = $this->getFontList($url);
            if ($fonts_json) {
                $font_array = json_decode($fonts_json, true);
                $font_key = array_search($font_family, array_column($font_array['items'], 'family'));
                $font = $font_array['items'][$font_key];
                if (! empty($font)) {
                    $font_string = '';
                    // Filter allowed styles/weights
                    $styles = array_intersect_key($font['files'], array_flip($allowed_styles));
                    foreach ($styles as $key => $file_url) {
                        $font_string .= $this->getFontString($font['family'], $key, $file_url);
                    }
                    $md5 = md5($font_string);
                    // Complete json string
                    $json_string = '{"md5":"' . $md5 . '","value":"' . $font_string . '"}';
                    $json_file = fopen(MUNICIPIO_PATH . 'assets/source/fonts/' . $font_file, 'w');
                    fwrite($json_file, $json_string);
                    fclose($json_file);
                }
            }
        }

        // Update font options
        update_option('theme_font_md5', $md5);
        update_option('theme_font_family', $font_family);
        update_option('theme_font_file', get_template_directory_uri() . '/assets/source/fonts/' . $font_file);
    }

    /**
     * Download font and convert to base64 encoded string
     * @param  string $font_family Font family name
     * @param  string $key         Font style name
     * @param  string $url         External font url
     * @return string
     */
    public function getFontString($font_family, $key, $url)
    {
        // Download font from url
        $file = file_get_contents($url);
        if ($file === false) {
            return '';
        }

        $font_style  = 'normal';
        $font_weight = 'normal';
        switch ($key) {
            case (is_numeric($key) ? true : false) :
                $font_weight = $key;
                break;
            case (ctype_alpha($key) ? true : false) :
                $font_style = $key;
                break;
            case (ctype_alnum($key) ? true : false) :
                preg_match('/[a-z]+/', $key, $match);
                $font_style = $match[0];
                preg_match('/[0-9]+/', $key, $match);
                $font_weight = $match[0];
                break;
            default:
                break;
        }

        // Base64 encode font file
        $base64 = 'data:application/x-font-woff' . ';base64,' . base64_encode($file);
        $font_string .= '@font-face {\n  font-family: \'' . $font_family . '\';\n  font-style: ' . $font_style . ';\n  font-weight: ' . $font_weight . ';\n  src: local(\'' . $font_family . '\'), local(\'' . $font_family . '-'. ucfirst($key) . '\'), url(' . $base64 . ') format(\'woff\');\n}\n';

        return $font_string;
    }

    /**
     * Get Google font list as json. Update existing list if possible
     * @param  string $url Google font url with api key
     * @return string      Json file content
     */
    public function getFontList($url = null)
    {
        $font_list  = MUNICIPIO_PATH . 'assets/source/fonts/google_fonts.json';
        $fonts_json = null;

        if (function_exists('wp_remote_get') && $url != null) {
            $response = wp_remote_get($url);
            if (isset($response['body']) && $response['body']) {
                // Save new font list to file
                if (strpos($response['body'], 'error') === false) {
                    $fonts_json = $response['body'];
                    file_put_contents($font_list, $fonts_json);
                }
            }
        }

        // Get local file
        if (!$fonts_json) {
            $fonts_json = file_get_contents($font_list);
        }

        return $fonts_json;
    }
}
