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

    public function load($template)
    {
        $search = basename($template);
        $view = locate_template($search);

        /**
         * Template not found. Return the original template path.
         */
        if (!$view) {
            return $template;
        }

        /**
         * Clean the view path
         */
        $view = str_replace(get_stylesheet_directory() . '/', '', $view);
        $view = str_replace('.blade.php', '', $view);

        /**
         * Template found
         * 1. Initialize controller (if exists)
         * 2. Fetch the data (with filter)
         * 3. Render the view
         */
        if (file_exists($this->CONTROLLER_PATH . '/' . basename($view) . '.php')) {
            $class = '\Municipio\Controller\\' . basename($view);
            new $class;
        }

        $data = array();
        $data = apply_filters('HbgBlade/data', $data);

        $blade = new Blade($this->VIEWS_PATH, $this->CACHE_PATH);
        echo $blade->view()->make($view, $data)->render();

        return false;
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
                    if (locate_template(array($type), false)) {
                        return $type;
                    }

                    return $original;
                });
            }
        }
    }
}
