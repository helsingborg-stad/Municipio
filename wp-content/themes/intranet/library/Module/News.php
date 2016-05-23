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
            'nameSingular' => __('Intranet news', 'municipio-intranet'),
            'namePlural' => __('Intranet news', 'municipio-intranet'),
            'description' => __('Shows news stories from the sites the current user is subscribing to (or from all if logged out)', 'municipio-intranet'),
            'supports' => array(),
            'icon' => 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAzMiAzMiI+PGcgZmlsbD0iIzAxMDAwMiI+PHBhdGggZD0iTTI5IDBIN2EzIDMgMCAwIDAtMyAzdjJIM2EzIDMgMCAwIDAtMyAzdjIwYTQgNCAwIDAgMCA0IDRoMjRhNCA0IDAgMCAwIDQtNFYzYTMgMyAwIDAgMC0zLTN6bTEgMjhjMCAxLjEwMi0uODk4IDItMiAySDRjLTEuMTAzIDAtMi0uODk4LTItMlY4YTEgMSAwIDAgMSAxLTFoMXYyMGExIDEgMCAwIDAgMiAwVjNhMSAxIDAgMCAxIDEtMWgyMmExIDEgMCAwIDEgMSAxdjI1eiIvPjxwYXRoIGQ9Ik0xOS40OTggMTMuMDA1aDhhLjUuNSAwIDAgMCAwLTFoLThhLjUuNSAwIDAgMCAwIDF6TTE5LjQ5OCAxMC4wMDVoOGEuNS41IDAgMCAwIDAtMWgtOGEuNS41IDAgMCAwIDAgMXpNMTkuNDk4IDcuMDA1aDhhLjUuNSAwIDAgMCAwLTFoLThhLjUuNSAwIDAgMCAwIDF6TTE2LjUgMjcuMDA0aC04YS41LjUgMCAwIDAgMCAxaDhhLjUuNSAwIDAgMCAwLTF6TTE2LjUgMjQuMDA0aC04YS41LjUgMCAwIDAgMCAxaDhhLjUuNSAwIDAgMCAwLTF6TTE2LjUgMjEuMDA0aC04YS41LjUgMCAwIDAgMCAxaDhhLjUuNSAwIDAgMCAwLTF6TTI3LjUgMjcuMDA0aC04YS41LjUgMCAwIDAgMCAxaDhhLjUuNSAwIDAgMCAwLTF6TTI3LjUgMjQuMDA0aC04YS41LjUgMCAwIDAgMCAxaDhhLjUuNSAwIDAgMCAwLTF6TTI3LjUgMjEuMDA0aC04YS41LjUgMCAwIDAgMCAxaDhhLjUuNSAwIDAgMCAwLTF6TTI3LjUgMTUuMDA0aC0xOWEuNS41IDAgMCAwIDAgMWgxOWEuNS41IDAgMCAwIDAtMXpNMjcuNSAxOC4wMDRoLTE5YS41LjUgMCAwIDAgMCAxaDE5YS41LjUgMCAwIDAgMC0xek05IDEzaDdhMSAxIDAgMCAwIDEtMVY1LjAwNGExIDEgMCAwIDAtMS0xSDlhMSAxIDAgMCAwLTEgMVYxMmExIDEgMCAwIDAgMSAxem0xLTdoNXY1aC01VjZ6Ii8+PC9nPjwvc3ZnPg=='
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
