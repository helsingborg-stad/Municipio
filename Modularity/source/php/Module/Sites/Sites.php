<?php

namespace Modularity\Module\Sites;

class Sites extends \Modularity\Module
{
    public $slug = 'sites';
    public $supports = array();
    public $multisiteOnly = true;
    public $isBlockCompatible = false;

    public function init()
    {
        $this->nameSingular = __('Sites', 'modularity');
        $this->namePlural = __('Sites', 'modularity');
        $this->description = __('Outputs a grid list with att sites in the network.', 'modularity');
    }

    public function data() : array
    {
        $data['sites'] = $this->getSites();
        return $data;
    }

    public function getSites()
    {
        $sites = get_sites();
        $includeMainSite = get_field('include_main_site', $this->ID);

        foreach ($sites as $key => &$site) {
            if (!$includeMainSite && is_main_site($site->blog_id)) {
                unset($sites[$key]);
                continue;
            }

            $site = get_blog_details($site->blog_id);

            switch_to_blog($site->blog_id);
            $site->description = get_option('blogdescription', null);
            restore_current_blog();

            $site->image = apply_filters('Modularity\Module\Sites\image', null, $site);
            $site->image_rendered = apply_filters('Modularity\Module\Sites\image_rendered', null, $site);
        }

        return $sites;
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
