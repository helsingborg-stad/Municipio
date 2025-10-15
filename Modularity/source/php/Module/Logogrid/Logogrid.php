<?php

namespace Modularity\Module\Logogrid;

class Logogrid extends \Modularity\Module
{
    public $slug = 'logogrid';
    public $supports = array();

    public function init()
    {
        $this->nameSingular = __('Logotype grid', 'modularity');
        $this->namePlural = __('Logotype grids', 'modularity');
        $this->description = __('Outputs a grid of logotypes', 'modularity');
    }

    public function data(): array
    {
        $logotypes = get_field('mod_logogrid', $this->ID);

        $stack = [];
        if (is_array($logotypes) && !empty($logotypes)) {
            foreach ($logotypes as $logotype) {
                $stack[] = [
                    'alt' => $logotype['mod_logogrid_name'],
                    'logo' => $logotype['mod_logogrid_image'],
                    'url' => $logotype['mod_logogrid_link']
                ];
            }
        }

        return ['list' => $stack];
    }

    public function template(): string
    {
        return 'grid.blade.php';
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
