<?php

namespace Modularity\Module\Map;

class Map extends \Modularity\Module
{
    public $slug = 'map';
    public $supports = array();

    protected $template = 'default';

    public function init()
    {
        $this->nameSingular = __('Map', 'modularity');
        $this->namePlural = __('Maps', 'modularity');
        $this->description = __("Outputs an embedded map.", 'modularity');

        add_filter('acf/load_field/name=map_url', array($this,'sslNotice'));
        add_filter('acf/load_value/name=map_url', array($this,'filterMapUrl'), 10, 3);
        add_filter('acf/update_value/name=map_url', array($this,'filterMapUrl'), 10, 3);
    }

    /**
     * This PHP function retrieves data based on certain conditions and returns either OpenStreetMap
     * template data or default template data.
     * 
     * @return array The `data()` function is returning either the result of the
     * `openStreetMapTemplateData()` function or the `defaultTemplateData()` function based on the
     * value of the `map_type` field in the `` array.
     */
    public function data() : array
    {
        $fields = $this->getFields();
        $data = array();

        //Shared template data
        $data['height'] = !empty($fields['height']) ? $fields['height'] : '400';

        //Set map type 
        if (empty($fields['map_type'])) {
            $fields['map_type'] = 'default';
        }
        $this->template = $fields['map_type'];

        //Handle as OpenStreetMap
        if ($fields['map_type'] == 'openStreetMap') {
            return $this->openStreetMapTemplateData($data, $fields);
        }

        //Handle as default
        return $this->defaultTemplateData($data, $fields);   
    }

    /**
     * The function `openStreetMapTemplateData` processes marker data and start position data for an
     * OpenStreetMap template.
     * 
     * @param data The `openStreetMapTemplateData` function takes two parameters: `` and
     * ``.
     * @param fields The `openStreetMapTemplateData` function takes two parameters: `` and
     * ``.
     * 
     * @return The function `openStreetMapTemplateData` is returning the modified `` array after
     * processing the input data and fields. The function adds pins with latitude, longitude, and
     * tooltip information to the `['pins']` array based on the provided markers. It also sets the
     * start position with latitude, longitude, and zoom level if the `osm_start_position` field is not
     * empty. Finally
     */
    private function openStreetMapTemplateData($data, $fields) {

        $data['pins'] = array();
        $start = $fields['osm_start_position'];

        if(!empty($fields['osm_markers']) && is_array($fields['osm_markers'])) {
            foreach ($fields['osm_markers'] as $marker) {
                if ($this->hasCorrectPlaceData($marker['position'])) {
                    $pin = array();
                    $pin['lat'] = $marker['position']['lat'];
                    $pin['lng'] = $marker['position']['lng'];
                    $pin['tooltip'] = $this->createMarkerTooltip($marker);

                    array_push($data['pins'], $pin);
                }
            }
        }

        if (!empty($start)) {
            $data['startPosition'] = [
                'lat' => $start['lat'], 
                'lng' => $start['lng'], 
                'zoom' => $start['zoom']
            ];
        }

        return $data;
    }
    
    /**
     * Generates default template data for the Map module.
     *
     * @param array $data The existing data array.
     * @param array $fields The fields array containing module settings.
     * @return array The updated data array with default template data.
     */
    private function defaultTemplateData($data, $fields) {
        //Get and sanitize url
        $map_url = $fields['map_url'];
        $map_url = str_replace('http://', 'https://', $map_url, $replaced); // Enforce ssl

        /**
         * If the scheme is not altered with str_replace, the url may only contain // without https:
         */
        if(0 === $replaced) {
            $parsedUrl = parse_url( $map_url );
            if(!isset($parsedUrl['scheme']) ) {
                $map_url = str_replace('//', 'https://', $map_url); // Ensure url scheme is literal
            }
        }

        $map_url = str_replace('disable_scroll=false', 'disable_scroll=true', $map_url); //Remove scroll arcgis

        //Create data array
        $data['map_url']            = $map_url;
        $data['map_description']    = !empty($fields['map_description']) ? $fields['map_description'] : '';
        
        $data['show_button']        = !empty($fields['show_button']) ? $fields['show_button'] : false;
        $data['button_label']       = !empty($fields['button_label']) ? $fields['button_label'] : false;
        $data['button_url']         = !empty($fields['button_url']) ? $fields['button_url'] : false;
        $data['more_info_button']   = !empty($fields['more_info_button']) ? $fields['more_info_button'] : false;
        $data['more_info']          = !empty($fields['more_info']) ? $fields['more_info'] : false;
        $data['more_info_title']    = !empty($fields['more_info_title']) ? $fields['more_info_title'] : false;

        $data['cardMapCss']         = ($data['more_info_button']) ? 'o-grid-12@xs o-grid-8@md' : 'o-grid-12@md';
        $data['cardMoreInfoCss']    = ($data['more_info_button']) ? 'o-grid-12@xs o-grid-4@md' : '';

        $data['uid']                = uniqid();
        $data['id']                 = $this->ID;

        $data['lang'] = [
            'knownLabels' => [
                'title' => __('We need your consent to continue', 'modularity'),
                'info' => sprintf(__('This part of the website shows content from %s. By continuing, <a href="%s"> you are accepting GDPR and privacy policy</a>.', 'modularity'), '{SUPPLIER_WEBSITE}', '{SUPPLIER_POLICY}'),
                'button' => __('I understand, continue.', 'modularity'),
            ],

            'unknownLabels' => [
                'title' => __('We need your consent to continue', 'modularity'),
                'info' => sprintf(__('This part of the website shows content from another website (%s). By continuing, you are accepting GDPR and privacy policy.', 'municipio'), '{SUPPLIER_WEBSITE}'),
                'button' => __('I understand, continue.', 'modularity'),
            ],
        ];

        return $data;
    }

    /**
     * The function checks if the position data contains non-empty latitude and longitude values.
     * 
     * @param position The `hasCorrectPlaceData` function is checking if the `position` parameter is
     * not empty and if it contains both `lat` and `lng` keys with non-empty values. This function
     * returns a boolean value indicating whether the `position` data is in the correct format.
     * 
     * @return bool a boolean value, either true or false.
     */
    private function hasCorrectPlaceData($position): bool {
        return !empty($position) && !empty($position['lat'] && !empty($position['lng']));
    }

   /**
    * The function createMarkerTooltip in PHP creates a tooltip array based on marker data.
    * 
    * @param marker The `createMarkerTooltip` function takes a `` parameter, which is expected
    * to be an associative array containing the following keys:
    * 
    * @return An array containing the title, excerpt, directions label, and directions URL of the
    * marker.
    */
    private function createMarkerTooltip($marker) {
        $tooltip = array();
        $tooltip['title'] = $marker['title'];
        $tooltip['excerpt'] = $marker['description'];
        $tooltip['directions']['label'] = $marker['link_text'];
        $tooltip['directions']['url'] = $marker['url'];

        return $tooltip;
    }

    /**
     * The function `sslNotice` adds a notice to a field if SSL is enabled or if a SSL proxy is being
     * used.
     * 
     * @param field The `sslNotice` function takes a parameter named ``, which seems to be an
     * array containing instructions for a map link. The function checks if SSL is enabled or if an SSL
     * proxy is being used, and if so, it modifies the instructions to include a notice about using
     * `https://`
     * 
     * @return The function `sslNotice` is returning the `` array with updated instructions if
     * the current connection is using SSL or an SSL proxy. The instructions will inform the user that
     * map links must start with `https://` for proper display.
     */
    public function sslNotice($field)
    {
        if (is_ssl() || $this->isUsingSSLProxy()) {
            $field['instructions'] = '<span style="color: #f00;">'.__("Your map link must start with http<strong>s</strong>://. Links without this prefix will not display.", 'modularity').'</span>';
        }

        return $field;
    }

    
    /**
     * The function `isUsingSSLProxy` checks if SSL proxy is being used based on the defined constant
     * `SSL_PROXY`.
     * 
     * @return The function `isUsingSSLProxy()` will return `true` if the constant `SSL_PROXY` is
     * defined and its value is `true`. Otherwise, it will return `false`.
     */
    private function isUsingSSLProxy()
    {
        if ((defined('SSL_PROXY') && SSL_PROXY === true)) {
            return true;
        }

        return false;
    }

    /**
     * Filter the map URL value.
     *
     * @param string $value The map URL value to be filtered.
     * @param int $post_id The ID of the post.
     * @param string $field The field name.
     * @return string The filtered map URL value.
     */
    public function filterMapUrl($value, $post_id, $field) 
    {
        $value = htmlspecialchars_decode($value);
        return $value;
    }

    /**
     * Returns the template file path for the Map module.
     *
     * @return string The template file path.
     */
    public function template() {
        $path = __DIR__ . "/views/" . $this->template . ".blade.php";

        if (file_exists($path)) {
            return $this->template . ".blade.php";
        }
        
        return 'default.blade.php';
    }

    /**
     * Available "magic" methods for modules:
     * init()            What to do on initialization
     * data()            Use to send data to view (return array)
     * style()           Enqueue style only when module is used on page
     * script            Enqueue script only when module is used on page
     * adminEnqueue()    Enqueue scripts for the module edit/add page in admin
     * template()        Return the view template (blade) the module should use when displayed
     */
}
