<?php

namespace Municipio;

use Philo\Blade\Blade as Blade;

class Template
{
    private $VIEWS_PATHS;
    private $CONTROLLER_PATH;
    private $CACHE_PATH;

    public function __construct()
    {
        add_filter('template_redirect', array($this, 'addTemplateFilters'));
        add_filter('template_include', array($this, 'load'));
        add_filter('get_search_form', array($this, 'getSearchForm'));

        $this->initCustomTemplates();

        /**
         * Set paths
         */
        $this->VIEWS_PATHS = apply_filters('Municipio/blade/view_paths', array(
            get_stylesheet_directory() . '/views',
            get_template_directory() . '/views'
        ));

        $this->VIEWS_PATHS = array_unique($this->VIEWS_PATHS);

        $this->CONTROLLER_PATH = get_template_directory() . '/library/Controller';

        $this->CACHE_PATH = WP_CONTENT_DIR . '/uploads/cache/blade-cache';
    }

    /**
     * Initializes custom templates
     * @return void
     */
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
     * Load controller and view
     * @param  string $template Template
     * @return mixed            Exception or false, false to make sure no
     *                          standard template file from wordpres is beeing included
     */
    public function load($template)
    {
        if (!empty(get_page_template_slug()) && get_page_template_slug() != $template) {
            $template = get_page_template_slug();
        }

        if (!\Municipio\Helper\Template::isBlade($template)) {
            $path = get_template_directory() . '/' . $template;

            // Return path if file exists, else default to page.blade.php
            if (file_exists($path)) {
                return $path;
            } else {
                \Municipio\Helper\Notice::add('View [' . $template . '] was not found. Defaulting to [page.blade.php].');
                $template = \Municipio\Helper\Template::locateTemplate('views/page.blade.php');
            }
        }

        // Clean the view path
        $view = $this->cleanViewPath($template);

        // Load view controller
        $controller = $this->loadController($view);

        // Render the view
        $data = null;
        if ($controller) {
            $data = $controller->getData();
        }

        $this->render($view, $data);

        return false;
    }

    /**
     * Loads controller for view template
     * @param  string $template Path to template
     * @return bool             True if controller loaded, else false
     */
    public function loadController($template)
    {
        $template = basename($template) . '.php';

        do_action('Municipio/blade/before_load_controller');
        $controller = \Municipio\Helper\Controller::locateController($template);

        if (!$controller) {
            $controller = get_template_directory() . '/library/Controller/BaseController.php';
        }

        require_once $controller;
        $namespace = \Municipio\Helper\Controller::getNamespace($controller);
        $class = '\\' . $namespace . '\\' . basename($controller, '.php');

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
        $data = apply_filters('Municipio/blade/data', $data);

        $blade = new Blade($this->VIEWS_PATHS, $this->CACHE_PATH);
        echo $blade->view()->make($view, $data)->render();
    }

    /**
     * Add a custom template
     * @param string $templateName Template name
     * @param string $templatePath Template path (relative to theme path)
     */
    public static function add($templateName, $templatePath)
    {
        add_filter('theme_page_templates', function ($templates) use ($templatePath, $templateName) {
            return array_merge(array(
                $templatePath => $templateName
            ), $templates);
        });

        return (object) array(
            'name' => $templateName,
            'path' => $templatePath
        );
    }

    public function addTemplateFilters()
    {
        $types = array(
            'index'      => 'index.blade.php',
            'home'       => 'index.blade.php',
            'single'     => 'single.blade.php',
            'page'       => 'page.blade.php',
            '404'        => '404.blade.php',
            'archive'    => 'archive.blade.php',
            'author'     => 'author.blade.php',
            'category'   => 'category.blade.php',
            'tag'        => 'tag.blade.php',
            'taxonomy'   => 'taxonomy.blade.php',
            'date'       => 'date.blade.php',
            'front-page' => 'index.blade.php',
            'paged'      => 'paged.blade.php',
            'search'     => 'search.blade.php',
            'single'     => 'single.blade.php',
            'singular'   => 'singular.blade.php',
            'attachment' => 'attachment.blade.php',
        );

        $types = apply_filters('Municipio/blade/template_types', $types);

        if (isset($types) && !empty($types) && is_array($types)) {
            foreach ($types as $key => $type) {
                add_filter($key . '_template', function ($original) use ($type, $types) {
                    if (empty($original) && is_front_page()) {
                        $type = $types['index'];
                    }

                    if ($templatePath = \Municipio\Helper\Template::locateTemplate($type)) {
                        return $templatePath;
                    }

                    return $original;
                });
            }
        }
    }

    public function cleanViewPath($view)
    {
        foreach ($this->VIEWS_PATHS as $path) {
            $view = str_replace($path . '/', '', $view);
        }

        $view = str_replace('.blade.php', '', $view);
        return $view;
    }
}
