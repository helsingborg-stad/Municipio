<?php

namespace Municipio;

use Philo\Blade\Blade as Blade;

class Template
{
    private $VIEWS_PATH;
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
        $this->VIEWS_PATH = get_stylesheet_directory();
        $this->CONTROLLER_PATH = get_stylesheet_directory() . '/library/Controller';
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

            $class = new $class;

            if (!method_exists($class, 'registerTemplate')) {
                continue;
            }

            $class->registerTemplate();
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

        $search = basename($template);
        $view = \Municipio\Helper\Template::locateTemplate($search);

        // Template not found, throw exception
        if (!$view) {
            \Municipio\Helper\Notice::add('View [' . $search . '] was not found. Defaulting to [page.blade.php].');
            $view = \Municipio\Helper\Template::locateTemplate('views/page.blade.php');
        }

        // Get queryed object
        $object = get_queried_object();

        // Handle taxonomy templates with specified type
        if (is_a($object, 'WP_Term')) {
            $view = \Municipio\Helper\Template::locateTemplate('taxonomy-' . $object->taxonomy . '.blade.php');
        }

        // Clean the view path
        $view = $this->cleanViewPath($view);

        // Load view controller
        $this->loadController($view);

        // Render the view
        $this->render($view);

        return false;
    }

    /**
     * Loads a view controller
     * @param  string $view The view name
     * @return bool False if nothing found, else returns the class object
     */
    public function loadController($view)
    {
        $class = ucwords($view, '-');
        $class = str_replace('-', '', $class);

        if (!file_exists($this->CONTROLLER_PATH . '/' . basename($class) . '.php')) {
            return false;
        }

        $class = '\Municipio\Controller\\' . basename($class);
        return new $class;
    }

    /**
     * Render a view
     * @param  string $view The view path
     * @return void
     */
    public function render($view)
    {
        $data = array();
        $data = apply_filters('HbgBlade/data', $data);

        $blade = new Blade($this->VIEWS_PATH, $this->CACHE_PATH);
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
            'front-page' => 'front-page.blade.php',
            'paged'      => 'paged.blade.php',
            'search'     => 'search.blade.php',
            'single'     => 'single.blade.php',
            'singular'   => 'singular.blade.php',
            'attachment' => 'attachment.blade.php',
        );

        $types = apply_filters('HbgBlade/template_types', $types);

        if (isset($types) && !empty($types) && is_array($types)) {
            foreach ($types as $key => $type) {
                add_filter($key . '_template', function ($original) use ($type) {
                    if (\Municipio\Helper\Template::locateTemplate($type)) {
                        return $type;
                    }

                    return $original;
                });
            }
        }
    }

    public function cleanViewPath($view)
    {
        $view = str_replace($this->VIEWS_PATH . '/', '', $view);
        $view = str_replace('.blade.php', '', $view);
        return $view;
    }
}
