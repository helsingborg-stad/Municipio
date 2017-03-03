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

        if (!file_exists($this->CACHE_PATH)) {
            if (!mkdir($this->CACHE_PATH, 0777, true)) {
                die("Could not create cache folder: " . $this->CACHE_PATH);
            }
        }

        add_action('init', array($this, 'adminFrontPageTemplates'));
        add_action('save_post', array($this, 'adminFrontPageTemplatesSave'));
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
        if ((is_page() || is_single() || is_front_page()) && !empty(get_page_template_slug()) && get_page_template_slug() != $template) {
            if (\Municipio\Helper\Template::locateTemplate(get_page_template_slug())) {
                $template = get_page_template_slug();
            }
        }

        if (!\Municipio\Helper\Template::isBlade($template)) {
            $path = $template;

            // Return path if file exists, else default to page.blade.php
            if (file_exists($path)) {
                return $path;
            } else {
                if (current_user_can('administrator')) {
                    \Municipio\Helper\Notice::add('<strong>' . __('Admin notice', 'municipio') . ':</strong> ' . sprintf(__('View [%s] was not found. Defaulting to [page.blade.php].', 'municipio'), $template), 'warning', 'pricon pricon-notice-warning');
                }

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

        if (basename($template) == '404.php') {
            $template = 'e404.php';
        }

        switch ($template) {
            case 'author.php':
                if (!defined('MUNICIPIO_BLOCK_AUTHOR_PAGES') || MUNICIPIO_BLOCK_AUTHOR_PAGES) {
                    $template = 'archive.php';
                }
                break;
        }

        $controller = \Municipio\Helper\Controller::locateController($template);

        if (!$controller) {
            $controller = \Municipio\Helper\Controller::locateController('BaseController');
        }

        $controller = apply_filters('Municipio/blade/controller', $controller);

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

    public function cleanViewPath($view)
    {
        foreach ($this->VIEWS_PATHS as $path) {
            $view = str_replace($path . '/', '', $view);
        }

        $view = str_replace('.blade.php', '', $view);
        return $view;
    }
}
