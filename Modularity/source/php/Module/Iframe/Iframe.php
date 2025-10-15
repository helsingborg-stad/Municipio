<?php

namespace Modularity\Module\Iframe;

class Iframe extends \Modularity\Module
{
    public $slug = 'iframe';
    public $supports = array();

    public function init()
    {
        $this->nameSingular = __('Iframe', 'modularity');
        $this->namePlural = __('Iframe', 'modularity');
        $this->description = __("Outputs an embedded page.", 'modularity');

        add_filter('acf/load_field/name=iframe_url', array($this,'sslNotice'));

       
    }

    public function data() : array
    {        
        $data['url']         = get_field('iframe_url', $this->ID);
        $data['height']      = get_field('iframe_height', $this->ID);
        $data['description'] = get_field('iframe_description', $this->ID);

        $data['lang'] = [
            'knownLabels' => [
                'title'  => __('We need your consent to continue', 'modularity'),
                'info'   => sprintf(__('This part of the website shows content from %s. By continuing, <a href="%s"> you are accepting GDPR and privacy policy</a>.', 'modularity'), '{SUPPLIER_WEBSITE}', '{SUPPLIER_POLICY}'),
                'button' => __('I understand, continue.', 'modularity'),
            ],

            'unknownLabels' => [
                'title'  => __('We need your consent to continue', 'modularity'),
                'info'   => sprintf(__('This part of the website shows content from another website (%s). By continuing, you are accepting GDPR and privacy policy.', 'municipio'), '{SUPPLIER_WEBSITE}'),
                'button' => __('I understand, continue.', 'modularity'),
            ],
        ];

        return $data;
    }

    public function sslNotice($field)
    {
        if (is_ssl() || $this->isUsingSSLProxy()) {
            $field['instructions'] = '<span style="color: #f00;">'.__("Your iframe link must start with http<strong>s</strong>://. Links without this prefix will not display.", 'modularity').'</span>';
        }

        return $field;
    }

    private function isUsingSSLProxy()
    {
        if ((defined('SSL_PROXY') && SSL_PROXY === true)) {
            return true;
        }

        return false;
    }

    public function script()
    {
        wp_localize_script(
            'modularity-'.$this->slug,
            'modIframe',
            array(
                'needConsent' => __('We need your consent to continue.', 'iframe-acceptance'),

            )
        );
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
