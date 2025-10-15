<?php

namespace Modularity\Module\Divider;

class Divider extends \Modularity\Module
{
    public $slug = 'divider';
    public $supports = array();
    private $curl; 

    public function init()
    {
        $this->nameSingular = __('Divider', 'modularity');
        $this->namePlural = __('Dividers', 'modularity');
        $this->description = __("Add a content divider.", 'modularity');
    }

    public function data() : array
    {
        //Get fields 
        $fields = $this->getFields();

        //Asign to view names
        $data['title']          = !empty($this->ID) && !empty($this->data['post_title']) ? $this->data['post_title'] : (!empty($fields['custom_block_title']) ? $fields['custom_block_title'] : false);
        $data['titleVariant']   = !empty($fields['divider_title_variant']) ? $fields['divider_title_variant'] : 'h2';
        
        //Send to view
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
