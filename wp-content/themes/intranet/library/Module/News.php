<?php

namespace Intranet\Module;

class News extends \Modularity\Module
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
            'id' => 'intranet-news',
            'nameSingular' => __('News', 'municipio-intranet'),
            'namePlural' => __('News', 'municipio-intranet'),
            'description' => __('Shows news stories from the sites the current user is subscribing to (or from all if logged out)', 'municipio-intranet'),
            'supports' => array(),
            'icon' => 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAyOTcgMjk3Ij48cGF0aCBkPSJNMTczLjg1OCAxMDQuNjI2aDcwLjQxdjUyLjQzMmgtNzAuNDF6Ii8+PHBhdGggZD0iTTQ0LjY3NyAyNjIuNDNoMjQyLjI1NmM1LjU2IDAgMTAuMDY3LTQuNTEgMTAuMDY3LTEwLjA3VjQ0LjY0YzAtNS41Ni00LjUwOC0xMC4wNjgtMTAuMDY3LTEwLjA2OEg0NC42NzdjLTUuNTYgMC0xMC4wNjcgNC41MDgtMTAuMDY3IDEwLjA2OHYyMDcuNzJjMCA1LjU2IDQuNTA3IDEwLjA3IDEwLjA2NyAxMC4wN3pNMTU3Ljc1IDk2LjU3YTguMDU1IDguMDU1IDAgMCAxIDguMDU0LTguMDU1aDg2LjUyYTguMDU1IDguMDU1IDAgMCAxIDguMDU1IDguMDU1djY4LjU0YTguMDU1IDguMDU1IDAgMCAxLTguMDU2IDguMDU0aC04Ni41MmE4LjA1NSA4LjA1NSAwIDAgMS04LjA1NC04LjA1NHYtNjguNTR6bS03OC40NjYtOC4wNTRoNTEuOTEzYTguMDU1IDguMDU1IDAgMCAxIDAgMTYuMTFINzkuMjg0YTguMDU0IDguMDU0IDAgMSAxIDAtMTYuMTF6bTAgMzQuNjJoNTEuOTEzYTguMDU1IDguMDU1IDAgMCAxIDAgMTYuMTFINzkuMjg0YTguMDU1IDguMDU1IDAgMSAxIDAtMTYuMTF6bTAgMzQuNjE2aDUxLjkxM2E4LjA1NSA4LjA1NSAwIDAgMSAwIDE2LjExSDc5LjI4NGE4LjA1NSA4LjA1NSAwIDEgMSAwLTE2LjExem0wIDUxLjkzMmgxNzMuMDRhOC4wNTYgOC4wNTYgMCAwIDEgMCAxNi4xMUg3OS4yODNhOC4wNTUgOC4wNTUgMCAwIDEgMC0xNi4xMXpNMTguNSAyNTIuMzZWNjkuMTkyaC04LjQzM0M0LjUwNyA2OS4xOTIgMCA3My43IDAgNzkuMjYydjE3My4xYzAgNS41NiA0LjUwOCAxMC4wNjcgMTAuMDY3IDEwLjA2N2gxMC40NWEyNi4wNTUgMjYuMDU1IDAgMCAxLTIuMDE2LTEwLjA3eiIvPjwvc3ZnPg=='
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
    }

    /**
     * Enqueue your scripts and/or styles with wp_enqueue_script / wp_enqueue_style
     * @return
     */
    public function enqueueAssets()
    {

    }
}
