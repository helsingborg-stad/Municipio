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

        /**
         * Set paths
         */
        $this->VIEWS_PATH = get_stylesheet_directory();
        $this->CONTROLLER_PATH = get_stylesheet_directory() . '/library/Controller';
        $this->CACHE_PATH = WP_CONTENT_DIR . '/uploads/cache/blade-cache';
    }

    /**
     * Check if and where template exists
     * @param  string $template        Template file name
     * @param  array  $additionalPaths Additional search paths
     * @return bool                    False if not found else path to template file
     */
    public static function locateTemplate($template, $additionalPaths = array())
    {
        $defaultPaths = array(
            get_stylesheet_directory() . '/views',
            get_stylesheet_directory(),
            get_template_directory() . '/views',
            get_template_directory()
        );

        $searchPaths = array_merge($defaultPaths, $additionalPaths);

        foreach ($searchPaths as $path) {
            $file = $path . '/' . str_replace('.blade.php', '', basename($template)) . '.blade.php';

            if (!file_exists($file)) {
                continue;
            }

            return $file;
        }

        return false;
    }

    public function load($template)
    {
        $search = basename($template);
        $view = self::locateTemplate($search);

        // Template not found, throw exception
        if (!$view) {
            return new \Exception('Template not found');
        }

        // Get queryed object
        $object = get_queried_object();

        // Handle taxonomy templates with specified type
        if (is_a($object, 'WP_Term')) {
            $view = $this->locateTemplate('taxonomy-' . $object->taxonomy . '.blade.php');
        }

        // Clean the view path
        $view = str_replace($this->VIEWS_PATH . '/', '', $view);
        $view = str_replace('.blade.php', '', $view);

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
                    if (\Municipio\Template::locateTemplate($type)) {
                        return $type;
                    }

                    return $original;
                });
            }
        }
    }
}
