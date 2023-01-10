<?php

namespace Municipio;

use ComponentLibrary\Init as ComponentLibraryInit;

class Template
{
    private $bladeEngine = null;
    private $viewPaths = null;

    public function __construct()
    {
        //Init custom tempaltes & views
        add_action('template_redirect', array($this, 'registerViewPaths'), 10);
        add_action('template_redirect', array($this, 'initCustomTemplates'), 10);

        // Initialize blade
        add_action('template_redirect', array($this, 'initializeBlade'), 15);

        //Loads custom controllers and views
        add_action('template_redirect', array($this, 'addTemplateFilters'), 10);

        add_filter('template_include', array($this, 'switchTemplate'), 5);
        add_filter('template_include', array($this, 'sanitizeViewName'), 10);
        add_filter('template_include', array($this, 'loadViewData'), 15);
    }

    /**
     * Get Viewpaths and Blade engine runtime.
     *
     * @return void
     */
    public function initializeBlade()
    {
        $this->viewPaths = $this->registerViewPaths();
        $init = new ComponentLibraryInit($this->viewPaths);
        $this->bladeEngine = $init->getEngine();
    }

    /**
     * Re-check if there is an custom template applied to the page.
     * This switches incorrect view data to a real template if exists.
     *
     * TODO: Investigate why we are getting faulty templates from
     * WordPress core functionality.
     *
     * @param string $view
     * @return string
     */
    public function switchTemplate($view)
    {

        $customTemplate = get_post_meta(get_queried_object_id(), '_wp_page_template', true);

        if ($customTemplate) {
            //Check if file exsists, before use
            if (is_array($this->viewPaths) && !empty($this->viewPaths)) {
                foreach ($this->viewPaths as $path) {
                    if (file_exists(rtrim($path, "/") . '/' . $customTemplate)) {
                        return $customTemplate;
                    }
                }
            }
        }

        $purpose = \Municipio\Helper\Purpose::getPurpose();
        if ($purpose) {
            if (is_singular()) {
                $view = MUNICIPIO_PATH . 'templates/' . $purpose . '/views/singular.blade.php';
            }

            if (is_post_type_archive()) {
                $view = MUNICIPIO_PATH . 'templates/' . $purpose . '/views/archive.blade.php';
            }
        }


        return $view;
    }

    /**
     * Register paths containing views
     * @return void
     */
    public function registerViewPaths(): array
    {
        if ($viewPaths = \Municipio\Helper\Template::getViewPaths()) {
            $externalViewPaths = apply_filters('Municipio/blade/view_paths', array());
            $viewPaths = array_merge($viewPaths, $externalViewPaths);

            return $viewPaths;
        } else {
            wp_die("No view paths registered, please register at least one.");
        }
    }

    /**
     * Initializes custom templates
     * @return void
     */
    public function initCustomTemplates(): void
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
     * @param $view
     */
    public function sanitizeViewName($view)
    {
        return $this->getViewNameFromPath($view);
    }

    /**
     * @param $view
     * @param array $data
     */
    public function loadViewData($view, $data = array())
    {

        $viewData = $this->accessProtected(
            $this->loadController(
                $this->getControllerNameFromView($view)
            ),
            'data'
        );

        $isArchive = fn() => is_archive() || is_home();
        $postType = get_post_type();
        $template = $viewData['template'] ?? '';

        $filters = [
            // [string $filterTag, array $filterParams, bool $useFilter, bool $isDeprecated],
            ['Municipio/Template/viewData', [], true, false],
            ['Municipio/Template/single/viewData', [$postType], is_single(), false],
            ['Municipio/Template/archive/viewData', [$postType, $template], $isArchive(), false],
            ["Municipio/Template/{$postType}/viewData", [], !empty($postType), false],
            ["Municipio/Template/{$postType}/single/viewData", [], is_single() && !empty($postType), false],
            ["Municipio/Template/{$postType}/archive/viewData", [$template], $isArchive() && !empty($postType), false],
        ];

        $deprecated = [
            [
                'Municipio/controller/base/view_data', [], true, true,
                '2.0', 'Municipio/Template/viewData'
            ],
            [
                'Municipio/blade/data', [], true, true,
                '3.0', 'Municipio/Template/viewData'
            ],

            // To be deprecated next
            [
                'Municipio/Controller/Archive/Data', [$postType, $template], $isArchive(), false,
                '3.0', 'Municipio/Template/archive/viewData'
            ],
            [
                'Municipio/viewData', [], true, false,
                '3.0', 'Municipio/Template/viewData'
            ],
        ];

        $tryApplyFilters = fn (array $viewData, array $maybeFilters): array => array_reduce(
            $maybeFilters,
            function ($viewData, $params) {
                [$filterTag, $filterParams, $useFilter, $isDeprecated, $version, $message] = [...$params, ...['', '']];

                $applyFilters =
                    fn (string $tag, array $value, array $params, bool $useDeprecated, string $v = '', string $m = '')
                    => $useDeprecated
                        ? apply_filters_deprecated($tag, array_merge([$value], $params), $v, $m)
                        : apply_filters($tag, ...array_merge([$value], $params));

                return $useFilter
                    ? $applyFilters($filterTag, $viewData, $filterParams, $isDeprecated, $version, $message)
                    : $viewData;
            },
            $viewData
        );

        $viewData = $tryApplyFilters($viewData, [...$filters, ...$deprecated]);

        return $this->renderView(
            (string) $view,
            (array)  $viewData
        );
    }

    /**
     * Loads controller for view template
     * @param string $template Path to template
     * @return object           The controller
     */
    public function loadController($template)
    {
        //Do something before controller creation
        do_action_deprecated('Municipio/blade/before_load_controller', $template, '3.0', 'Municipio/blade/beforeLoadController');

        //Handle 404 renaming
        if ($template == '404') {
            $template = 'E404';
        }

        //Locate default controller
        $controller = \Municipio\Helper\Controller::locateController($template);

        // Locate purpose controller
        // TODO Make sure pageForPosttype also is handled correctly here
        // TODO Also check that the controller actually exists before returning it
        if (is_a(get_queried_object(), 'WP_Post_Type')) {
            $purpose = \Municipio\Helper\Purpose::getPurpose(get_queried_object());
            if ($purpose) {
                $controller = \Municipio\Helper\Controller::locateController('Archive' . ucfirst($purpose));
            }
        } elseif (is_a(get_queried_object(), 'WP_Post')) {
            $purpose = \Municipio\Helper\Purpose::getPurpose(get_queried_object()->post_type);
            if ($purpose) {
                $controller = \Municipio\Helper\Controller::locateController('Singular' . ucfirst($purpose));
            }
        }

        //Locate fallback controller
        if (!$controller && is_numeric(get_queried_object_id())) {
            $controller = \Municipio\Helper\Controller::locateController('Singular');
        } elseif (!$controller) {
            $controller = \Municipio\Helper\Controller::locateController('BaseController');
        }

        //Filter
        $controller = apply_filters('Municipio/blade/controller', $controller);

        //Require controller
        require_once $controller;
        $namespace = \Municipio\Helper\Controller::getNamespace($controller);
        $class = '\\' . $namespace . '\\' . basename($controller, '.php');

        //Do something after controller creation
        do_action_deprecated('Municipio/blade/after_load_controller', $template, '3.0', 'Municipio/blade/afterLoadController');

        return new $class();
    }

    /**
     * @param $view
     * @param array $data
     */
    public function renderView($view, $data = array())
    {
        try {
            $markup = $this->bladeEngine->make(
                $view,
                array_merge(
                    $data,
                    array('errorMessage' => false)
                )
            )->render();

            // Adds the option to make html more readable.
            // This is a option that is intended for permanent
            // use. But cannot be implemented due to some html
            // issues.
            if (class_exists('tidy') && isset($_GET['tidy'])) {
                $tidy = new \tidy;

                $tidy->parseString($markup, [
                    'indent'         => true,
                    'output-xhtml'   => false,
                    'wrap'           => PHP_INT_MAX
                ], 'utf8');

                $tidy->cleanRepair();

                echo $tidy;
            } else {
                echo $markup;
            }
        } catch (\Throwable $e) {
            echo '<pre style="border: 3px solid #f00; padding: 10px;">';
            echo '<strong>' . $e->getMessage() . '</strong>';
            echo '<hr style="background: #000; outline: none; border:none; display: block; height: 1px;"/>';
            echo $e->getTraceAsString();
            echo '</pre>';
        }

        return false;
    }

    /**
     * Get a view clean view path
     * @param string $view The view path
     * @return void
     */
    private function getViewNameFromPath($view)
    {
        //Remove all paths
        $view = str_replace(
            \Municipio\Helper\Template::getViewPaths(),
            "",
            $view
        ); // Remove view path

        //Remove suffix
        $view = trim(str_replace(".blade.php", "", $view), "/");

        return str_replace("/", ".", $view);
    }

    /**
     * Get a controller name
     * @param string $view The view path
     * @return void
     */
    private function getControllerNameFromView($view)
    {
        return str_replace(".", "", ucwords($view));
    }

    /**
     * Filter template name (what to look for)
     * @return string
     */
    public function addTemplateFilters()
    {
        $types = array(
            'index' => 'index.blade.php',
            'home' => 'archive.blade.php',
            'single' => 'single.blade.php',
            'page' => 'page.blade.php',
            '404' => '404.blade.php',
            'archive' => 'archive.blade.php',
            'author' => 'author.blade.php',
            'category' => 'category.blade.php',
            'tag' => 'tag.blade.php',
            'taxonomy' => 'taxonomy.blade.php',
            'date' => 'date.blade.php',
            'front-page' => 'front-page.blade.php',
            'paged' => 'paged.blade.php',
            'search' => 'search.blade.php',
            'singular' => 'singular.blade.php',
            'attachment' => 'attachment.blade.php',
        );

        $types = apply_filters_deprecated('Municipio/blade/template_types', [$types], '3.0', 'Municipio/blade/templateTypes');

        if (isset($types) && !empty($types) && is_array($types)) {
            foreach ($types as $key => $type) {
                add_filter($key . '_template', function ($original) use ($key, $type, $types) {
                    // Front page
                    if (empty($original) && is_front_page()) {
                        $type = $types['front-page'];
                    }

                    // Template slug
                    if (get_page_template_slug()) {
                        $type = get_page_template_slug();
                    }

                    $templatePath = \Municipio\Helper\Template::locateTemplate($type);

                    // Look for post type archive
                    global $wp_query;
                    if (is_post_type_archive() && isset($wp_query->query['post_type'])) {
                        $search = 'archive-' . $wp_query->query['post_type'] . '.blade.php';

                        if ($found = \Municipio\Helper\Template::locateTemplate($search)) {
                            $templatePath = $found;
                        }
                    }

                    // Look for post type single page
                    if (is_single() && isset($wp_query->query['post_type'])) {
                        $search = 'single-' . $wp_query->query['post_type'] . '.blade.php';
                        if ($found = \Municipio\Helper\Template::locateTemplate($search)) {
                            $templatePath = $found;
                        }
                    }

                    // Transformation made
                    if ($templatePath) {
                        return $templatePath;
                    }

                    // No changes needed
                    return $original;
                });
            }
        }
    }

    /**
     * Proxy for accessing provate props
     * @return mixed Array of values
     */
    public function accessProtected($obj, $prop)
    {
        $reflection = new \ReflectionClass($obj);
        $property = $reflection->getProperty($prop);
        $property->setAccessible(true);
        return $property->getValue($obj);
    }

    public function cleanViewPath($view)
    {
        $viewPaths = \Municipio\Helper\Template::getViewPaths();
        foreach ($viewPaths as $path) {
            $view = str_replace($path . '/', '', $view);
        }

        $view = str_replace('.blade.php', '', $view);
        return $view;
    }
}
