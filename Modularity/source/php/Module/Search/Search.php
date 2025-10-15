<?php

namespace Modularity\Module\Search;

class Search extends \Modularity\Module
{
    public $slug = 'search';
    public $supports = array();
    public $isBlockCompatible = true;

    public function init()
    {
        $this->nameSingular = __("Search", 'modularity');
        $this->namePlural = __("Search", 'modularity');
        $this->description = __("Outputs a search form.", 'modularity');
    }

    public function data() : array
    {
        $data = array();
        $fields = $this->getFields();

        $data['placeholder'] = $fields['mod_search_placeholder'] ?? '';
        $data['buttonLabel'] = $fields['mod_search_button_label'] ?? '';
        $data['width'] = $fields['mod_search_width'] ?? '100';
        $data['align'] = $fields['mod_search_alignment'] ?? 'start';

        $data['homeUrl'] = esc_url(get_home_url());

        return $data;
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
