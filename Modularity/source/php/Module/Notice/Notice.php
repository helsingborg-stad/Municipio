<?php

namespace Modularity\Module\Notice;

class Notice extends \Modularity\Module
{
    public $slug = 'notice';
    public $supports = array();
    public $blockSupports = array(
        'align' => ['full']
    );

    public function init()
    {
        $this->nameSingular = __("Notice", 'modularity');
        $this->namePlural = __("Notice", 'modularity');
        $this->description = __("Outputs a notice", 'modularity');
    }

    public function data(): array
    {
        $data = $this->getFields();
        $data['icon'] = $this->iconData(
            $data['notice_type']
        );

        return $data;
    }

    public function iconData($icon)
    {
        $icons = [
            'info'      => 'info',
            'success'   => 'check_circle',
            'warning'   => 'warning',
            'danger'    => 'error'
        ];

        return [
            'name' => $icons[$icon]
        ];
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
