<?php

namespace Intranet\Module;

class IncidentList extends \Modularity\Module
{

    /**
     * Module args
     * @var array
     */
    public $args = array();

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->args = array(
            'id' => 'intranet-incident-list',
            'nameSingular' => __('Incident list', 'municipio-intranet'),
            'namePlural' => __('Incident lists', 'municipio-intranet'),
            'description' => __('Lists incidents', 'municipio-intranet'),
            'supports' => array(),
            'icon' => 'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4KPCEtLSBHZW5lcmF0b3I6IEFkb2JlIElsbHVzdHJhdG9yIDIwLjEuMCwgU1ZHIEV4cG9ydCBQbHVnLUluIC4gU1ZHIFZlcnNpb246IDYuMDAgQnVpbGQgMCkgIC0tPgo8c3ZnIHZlcnNpb249IjEuMSIgaWQ9IkxheWVyXzEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiIHg9IjBweCIgeT0iMHB4IgoJIHZpZXdCb3g9IjAgMCAyNCAyNCIgc3R5bGU9ImVuYWJsZS1iYWNrZ3JvdW5kOm5ldyAwIDAgMjQgMjQ7IiB4bWw6c3BhY2U9InByZXNlcnZlIj4KPHBhdGggZD0iTTcuMSwxLjRDNy4xLDAuNiw3LjcsMCw4LjUsMGMwLjksMCwxLjYsMC45LDEuNCwxLjhjLTAuMSwwLjMsMCwwLjcsMC4xLDFjMC40LDAuNywxLjEsMS4xLDIsMS4xYzAuOSwwLDEuNi0wLjQsMi0xLjEKCWMwLjItMC4zLDAuMi0wLjcsMC4xLTFDMTMuOSwwLjksMTQuNiwwLDE1LjUsMGMwLjgsMCwxLjQsMC42LDEuNCwxLjRjMCwwLjctMC41LDEuMi0xLjEsMS40Yy0wLjMsMC4xLTAuNiwwLjMtMC44LDAuNQoJYy0wLjYsMSwwLjIsMS45LDAuOSwyLjhDMTQuOCw2LjcsMTMuNCw3LDEyLDdjLTEuNSwwLTIuOC0wLjMtNC0wLjhDOC44LDUuMiw5LjYsNC40LDksMy4zQzguOCwzLDguNSwyLjksOC4yLDIuOAoJQzcuNSwyLjYsNy4xLDIuMSw3LjEsMS40eiBNMjAuNiwxNS41aDIuNGMwLjYsMCwxLjEtMC41LDEuMS0xcy0wLjUtMS0xLjEtMWgtMi40Yy0wLjYsMC0xLjEtMC40LTEuMi0xLjFjLTAuMS0wLjcsMC4zLTEuMSwwLjgtMS4zCglsMi4yLTAuOWMwLjUtMC4yLDAuOC0wLjgsMC42LTEuM2MtMC4yLTAuNS0wLjgtMC44LTEuNC0wLjZsLTIuMywwLjljLTAuMywwLjEtMC43LDAtMC45LTAuNGMtMC4yLTAuNC0wLjQtMC44LTAuNi0xLjIKCUMxNiw4LjUsMTQuMSw5LDEyLDlDOS45LDksOCw4LjUsNi4zLDcuNUM2LjEsNy45LDUuOSw4LjIsNS43LDguNkM1LjUsOS4yLDUuMSw5LjIsNC44LDkuMUwyLjUsOC4yQzEuOSw4LDEuMyw4LjIsMS4xLDguOAoJYy0wLjIsMC41LDAsMS4xLDAuNiwxLjNjMCwwLDAsMCwwLDBMMy45LDExYzAuNSwwLjIsMC45LDAuNywwLjgsMS4zQzQuNiwxMyw0LDEzLjQsMy41LDEzLjRIMS4xYy0wLjYsMC0xLjEsMC41LTEuMSwxczAuNSwxLDEuMSwxCgloMi40YzAuNiwwLDEuMiwwLjQsMS4zLDEuMUM0LjgsMTcuMiw0LjUsMTcuNyw0LDE4TDEuNSwxOUMxLDE5LjMsMC44LDE5LjksMSwyMC40czAuOSwwLjgsMS40LDAuNUw1LDE5LjhjMC4zLTAuMSwwLjctMC4xLDAuOSwwLjMKCWMxLjMsMi4yLDMuNiwzLjcsNi4xLDMuOWMyLjUtMC4zLDQuOC0xLjcsNi4xLTMuOGMwLjMtMC40LDAuNi0wLjUsMS0wLjRsMi42LDEuMWMwLjUsMC4yLDEuMiwwLDEuNC0wLjVjMC4yLTAuNSwwLTEuMS0wLjUtMS40CgljMCwwLDAsMCwwLDBMMjAsMThjLTAuNS0wLjItMC45LTAuNy0wLjgtMS40QzE5LjQsMTUuOCwyMCwxNS41LDIwLjYsMTUuNUwyMC42LDE1LjV6Ii8+Cjwvc3ZnPgo='
        );

        // This will register the module
        $this->register(
            $this->args['id'],
            $this->args['nameSingular'],
            $this->args['namePlural'],
            $this->args['description'],
            $this->args['supports'],
            $this->args['icon']
        );

        // Enqueue action
        //add_action('Modularity/Module/' . $this->moduleSlug . '/enqueue', array($this, 'enqueueAssets'));

        // Add our template folder as search path for templates
        add_filter('Modularity/Module/TemplatePath', function ($paths) {
            $paths[] = INTRANET_PATH . 'templates/';
            return $paths;
        });

        add_filter('Modularity/Display/' . $this->moduleSlug . '/Markup', array($this, 'hideIfEmpty'), 10, 2);
    }

    /**
     * Hides the module if no incidents
     * @param  string $markup Markup
     * @param  object $module Module post object
     * @return string         Markup
     */
    public function hideIfEmpty($markup, $module)
    {
        $sites = get_field('incidents', $module->ID);
        $level = get_field('incident_level', $module->ID);
        $length = get_field('length', $module->ID);

        $incidents = \Intranet\CustomPostType\Incidents::getIncidents($sites, $level, $length);

        if (count($incidents) === 0) {
            return '';
        }

        return $markup;
    }

    /**
     * Enqueue your scripts and/or styles with wp_enqueue_script / wp_enqueue_style
     * @return
     */
    public function enqueueAssets()
    {

    }
}
