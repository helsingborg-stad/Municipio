<?php

namespace Municipio\Theme;

class ColorScheme
{
    private $optionName = "color_scheme_palette";

    public function __construct()
    {
        add_action( 'admin_enqueue_scripts', array($this, 'colorPickerDefaultPalette'), 1000);
        add_filter('acf/update_value/name=color_scheme', function ($value, $post_id, $field) {
            if (in_array($post_id, array("option", "options"))) {
                $this->getRemoteColorScheme($value);
            }
            return $value;
        }, 10, 3);
    }

    /**
     * Localize theme colors to set color picker default colors
     * @return void
     */
    public function colorPickerDefaultPalette() {

        if (!get_field('color_scheme', 'options')) {
            return;
        }

        if (!get_option($this->optionName) || !is_array(get_option($this->optionName)) || empty(get_option($this->optionName))) {
            $this->getRemoteColorScheme(get_field('color_scheme', 'options'));
        }

        $colors = (array) apply_filters( 'Municipio/Theme/ColorPickerDefaultPalette', get_option($this->optionName));

        wp_localize_script( 'helsingborg-se-admin', 'themeColorPalette', [
            'colors' => $colors,
        ]);
    }

    /**
     * Get remote colorsheme from styleguide etc
     * @param  string $manifestId. A identifier that represents the id of the theme configuration (filename on server)
     * @return bool
     */
    public function getRemoteColorScheme($manifestId = "") : bool
    {
        if (!defined('MUNICIPIO_STYLEGUIDE_URI')) {
            return false;
        }

        if (empty($manifestId)) {
            $manifestId = apply_filters('Municipio/theme/key', get_field('color_scheme', 'option'));
        }

        $args = (defined('DEV_MODE') && DEV_MODE == true) ? ['sslverify' => false] : array();

        //Get remote data
        $request = wp_remote_get("https:" . MUNICIPIO_STYLEGUIDE_URI . "vars/" . $manifestId . '.json', $args);

        //Store if valid response
        if (wp_remote_retrieve_response_code($request) == 200) {
            if (!empty($response = json_decode(wp_remote_retrieve_body($request)))) {
                $this->storeColorScheme($response);
            }

            return true;
        }

        //Not updated
        return false;
    }

    /**
     * Stores the colorsheme details in the database for use by other plugins
     * @param  string $colors. Contains a flat array of colors HEX to store.
     * @return bool
     */

    public function storeColorScheme($colors) : bool
    {
        if (!is_array($colors) && !is_object($colors)) {
            $colors = array();
        }

        return update_option($this->optionName, (array) $this->sanitizeColorSheme($colors), false);
    }

    /**
     * Get the color sheme details
     * @return array
     */

    public function getStoredColorSheme()
    {
        return get_option($this->optionName);
    }

    /**
     * Remove duplicates etc and make the array stored keyless
     * @param  array/object $colors A unsanitized arry of colors (must be flat)
     * @return array
     */

    public function sanitizeColorSheme($colors)
    {

        //Make array keyless
        $colors = array_values(array_unique((array) $colors));

        //Check if value is valid HEX
        foreach ($colors as $colorKey => $color) {
            if (preg_match('/^#[a-f0-9]{6}$/i', $color)) {
                continue;
            }
            unset($colors[$colorKey]);
        }

        //Sort (base colors at the end)
        usort($colors, function ($a, $b) {
            return strlen($b)-strlen($a);
        });

        return $colors;
    }
}
