<?php

namespace Modularity\Module\Spacer;

class Spacer extends \Modularity\Module
{
    public $slug = 'spacer';
    public $supports = array();

    public function init()
    {
        $this->nameSingular = __('Spacer', 'modularity');
        $this->namePlural = __('Spacers', 'modularity');
        $this->description = __("Outputs whitespace.", 'modularity');
    }

    public function data() : array
    {
        $fields = $this->getFields();

        $data = array(
            'amount' => $fields['space_amount'] ?? 4,
        );

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
