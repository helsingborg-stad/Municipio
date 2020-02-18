<?php

namespace Municipio;

use HelsingborgStad\GlobalBladeEngine as Blade;
use BladeComponentLibrary as ComponentLibrary;

class Template 
{

    public function __construct() {
        

        add_action('init', function() {
            //new ComponentLibrary\init();
        }, 8); 


        //Register view path
        add_action('init', array($this, 'initCustomTemplates')); 
        add_action('wp', array($this, 'registerViewPaths'));
        add_filter('template_redirect', array($this, 'addTemplateFilters'));
        add_filter('template_include', array($this, 'load'));
        add_filter('get_search_form', array($this, 'getSearchForm'));

        $this->controllerPath = get_template_directory() . '/library/Controller';

        add_action('init', array($this, 'adminFrontPageTemplates'));
        add_action('save_post', array($this, 'adminFrontPageTemplatesSave'));
    }


    /**
     * Load controller and view
     * @param  string $template Template
     * @return mixed            Exception or false, false to make sure no
     *                          standard template file from wordpres is beeing included
     */
    public function load($template)
    {

        if ((is_page() || is_single() || is_front_page()) && !empty(get_page_template_slug()) && get_page_template_slug() != $template) {
            if (\Municipio\Helper\Template::locateTemplate(get_page_template_slug())) {
                $template = get_page_template_slug();
            }
        }

        if (!\Municipio\Helper\Template::isBlade($template)) {

            // Return path if file exists, else default to page.blade.php
            if (file_exists($template)) {
                return $template;
            }

            if (current_user_can('administrator')) {
                \Municipio\Helper\Notice::add('<strong>' . __('Admin notice', 'municipio') . ':</strong> ' . sprintf(__('View [%s] was not found. Defaulting to [page.blade.php].', 'municipio'), $template), 'warning', 'pricon pricon-notice-warning');
            }

            //TODO: Check why we include views/ prefix here 
            $template = \Municipio\Helper\Template::locateTemplate('views/page.blade.php');
        }

        // Clean the view path
        $view = $this->cleanViewPath($template);

        // Load view controller
        $controller = $this->loadController($view);

        // Get controller data 
        if ($controller) {
            $data = $controller->getData();
        }

        // Render view with data, if any
        $this->render($view, isset($data) ? $data : null);

        //Stor excec
        return false;
    }

    /**
     * Register paths containing views
     * @return void
     */
    public function registerViewPaths() {
        if($viewPaths = \Municipio\Helper\Template::getViewPaths()) {
            foreach($viewPaths as $path) {
                Blade::addViewPath(rtrim($path, DIRECTORY_SEPARATOR), true);
            }
        } else {
            wp_die("No view paths registered, please register at least one."); 
        }
    }

    public function adminFrontPageTemplates()
    {
        if (!is_admin() || !isset($_GET['post']) || $_GET['post'] != get_option('page_on_front')) {
            return;
        }

        \Municipio\Helper\Template::add(__('Page', 'municipio'), \Municipio\Helper\Template::locateTemplate('page.blade.php'));

        add_filter('gettext', function ($translation, $text, $domain) {
            if ($text == 'Default Template') {
                return __('Front page', 'municipio');
            }

            return $translation;
        }, 10, 3);
    }

    public function adminFrontPageTemplatesSave($postId)
    {
        if (!isset($_POST['page_template']) || empty($_POST['page_template'])) {
            return;
        }

        update_post_meta($postId, '_wp_page_template', $_POST['page_template']);
    }

    /**
     * Initializes custom templates
     * @return void
     */

    //TODO: Enable template controllers to be versionated as in Helpers/Controller -> L12     
    public function initCustomTemplates()
    {
        $directory = MUNICIPIO_PATH . 'library/Controller/';

        foreach (@glob($directory . "*.php") as $file) {
            $class = '\Municipio\Controller\\' . basename($file, '.php');

            if (!class_exists($class)) {
                continue;
            }

            if (!method_exists($class, 'registerTemplate')) {
                continue;
            }

            $class::registerTemplate();
            unset($class);
        }
    }

    /**
     * Get searchform template
     * @param  string $searchform Original markup
     * @return mixed
     */
    //TODO: Enable template controllers to be versionated as in Helpers/Controller -> L12    
    public function getSearchForm($searchform)
    {
        if ($view = \Municipio\Helper\Template::locateTemplate('searchform.blade.php')) {
            $view = $this->cleanViewPath($view);
            $this->loadController($view);
            $this->render($view);
            return false;
        }

        return $searchform;
    }

    

    /**
     * Loads controller for view template
     * @param  string $template Path to template
     * @return object           The controller
     */
    public function loadController($template)
    {
        //Do something before controller creation
        do_action('Municipio/blade/before_load_controller');

        //Handle 404 renaming
        if ($template == '404') {
            $template = 'e404.php';
        }

        //Locate controller
        if (!$controller = \Municipio\Helper\Controller::locateController($template)) {
            $controller = \Municipio\Helper\Controller::locateController('BaseController');
        }

        //Filter 
        $controller = apply_filters('Municipio/blade/controller', $controller);

        //Require controller
        require_once $controller;
        $namespace = \Municipio\Helper\Controller::getNamespace($controller);
        $class = '\\' . $namespace . '\\' . basename($controller, '.php');

        //Do something after controller creation
        do_action('Municipio/blade/after_load_controller');

        return new $class();
    }

    /**
     * Render a view
     * @param  string $view The view path
     * @return void
     */
    public function render($view, $data = array())
    {
        echo Blade::instance()->make(
            $view,
            apply_filters('Municipio/blade/data', $data)
        )->render();
    }

    /**
     * Filter template name (what to look for)
     * @return string
     */
    public function addTemplateFilters()
    {
        $types = array(
            'index'      => 'index.blade.php',
            'home'       => 'archive.blade.php',
            'single'     => 'single.blade.php',
            'page'       => 'page.blade.php',
            '404'        => '404.blade.php',
            'archive'    => 'archive.blade.php',
            'author'     => 'author.blade.php',
            'category'   => 'category.blade.php',
            'tag'        => 'tag.blade.php',
            'taxonomy'   => 'taxonomy.blade.php',
            'date'       => 'date.blade.php',
            'front-page' => 'front-page.blade.php',
            'paged'      => 'paged.blade.php',
            'search'     => 'search.blade.php',
            'single'     => 'single.blade.php',
            'singular'   => 'singular.blade.php',
            'attachment' => 'attachment.blade.php',
        );

        // TODO: Depricate, change to camel cased alternative. 
        $types = apply_filters('Municipio/blade/template_types', $types);

        if (isset($types) && !empty($types) && is_array($types)) {
            foreach ($types as $key => $type) {
                add_filter($key . '_template', function ($original) use ($key, $type, $types) {
                    if (empty($original) && is_front_page()) {
                        $type = $types['front-page'];
                    }

                    $templatePath = \Municipio\Helper\Template::locateTemplate($type);

                    // Look for post type archive
                    global $wp_query;
                    if (is_post_type_archive() && isset($wp_query->query['post_type'])) {
                        $search = 'archive-' . $wp_query->query['post_type'] . '.blade.php';
                        $found = \Municipio\Helper\Template::locateTemplate($search);

                        if ($found) {
                            $templatePath = $found;
                        }
                    }

                    // Look for post type single page
                    if (is_single() && isset($wp_query->query['post_type'])) {
                        $search = 'single-' . $wp_query->query['post_type'] . '.blade.php';
                        $found = \Municipio\Helper\Template::locateTemplate($search);

                        if ($found) {
                            $templatePath = $found;
                        }
                    }

                    if ($templatePath) {
                        return $templatePath;
                    }

                    return $original;
                });
            }
        }
    }

    /**
     * Get a view clean view path
     * @param  string $view The view path
     * @return void
     */
    public function cleanViewPath($view)
    {

        var_dump($view); 

        foreach (\Municipio\Helper\Template::getViewPaths() as $pathKey => $path) {
            $view = str_replace($path . '/', '', $view);
        }

        return str_replace('.blade.php', '', $view);
    }
    
}
