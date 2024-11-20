<?php

namespace Municipio;

use WpService\WpService;
use AcfService\AcfService;
use ComponentLibrary\Init;
use HelsingborgStad\BladeService\BladeServiceInterface;
use Municipio\Config\Features\SchemaData\SchemaDataConfigInterface;
use Municipio\Controller\Navigation\MenuBuilderInterface;
use Municipio\Controller\Navigation\MenuDirector;
use Municipio\Helper\Controller as ControllerHelper;
use Municipio\Helper\Template as TemplateHelper;

/**
 * Class Template
 * @package Municipio
 */
class Template
{
    private ?BladeServiceInterface $bladeEngine = null;
    private ?array $viewPaths                   = null;

    /**
     * Template constructor.
     * @param MenuBuilderInterface $menuBuilder
     * @param MenuDirector $menuDirector
     * @param AcfService $acfService
     * @param WpService $wpService
     * @param SchemaDataConfigInterface $schemaDataConfig
     */
    public function __construct(
        private MenuBuilderInterface $menuBuilder,
        private MenuDirector $menuDirector,
        private AcfService $acfService,
        private WpService $wpService,
        private SchemaDataConfigInterface $schemaDataConfig
    ) {
        //Init custom templates & views
        add_action('template_redirect', array($this, 'registerViewPaths'), 10);
        add_action('template_redirect', array($this, 'initCustomTemplates'), 10);

        // Initialize blade
        add_action('template_redirect', array($this, 'initializeBlade'), 15);

        //Loads custom controllers and views
        add_action('template_redirect', array($this, 'addTemplateFilters'), 10);

        add_filter('template_include', array($this, 'switchPageTemplate'), 5);
        add_filter('template_include', array($this, 'sanitizeViewName'), 10);

        add_filter('template_include', array($this, 'loadViewData'), 15);
    }

    /**
     * @param string $view The template currently loaded.
     * @param array $data void
     *
     * @return the rendered view.
     */
    public function loadViewData(string $originalTemplate = '', $data = array())
    {
        $controller = $this->loadController($originalTemplate);
        $viewData   = $this->accessProtected($controller['data'], 'data');

        // Set view based on controller instructions, if any.
        // First checks $controller['data']->view, then $controller['view']
        $view = $controller['data']->view ?? $controller['view'] ?? null;

        if (empty(TemplateHelper::locateView($view))) {
            // If set view doesn't exist, fall back to original template.
            // Or if original template is empty, fall back to 'page'.
            $view = $originalTemplate ?: 'page';
        }

        $isArchive = fn() => is_archive() || is_home();
        $postType  = $this->wpService->getPostType();
        $template  = $viewData['template'] ?? '';

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
    * Loads a controller
    *
    * Controllers will be applied in ascending order of priority: 0 first, 1 second, 2 third, etc.
    * This means that a controller that has a priority of 0 will be applied first.
    * Only one controller can be applied at a time and the method will exit once it's been applied.
    *
    * @param string template The WordPress template name, e.g. page, archive, 404, etc.
    *
    * @return object A new instance of the controller class.
    */
    public function loadController(string $template = ''): array
    {
        if (!is_post_publicly_viewable() && !is_user_logged_in() && !is_search() && !is_archive()) {
            $template = '404';
        }

        if (is_archive() && ($template === 'one-page' || $template === 'page-centered')) {
            $template = 'archive';
        }

        //Do something before controller creation
        do_action_deprecated(
            'Municipio/blade/before_load_controller',
            $template,
            '3.0',
            'Municipio/blade/beforeLoadController'
        );

        // Controller conditions
        $isSingular                  = fn() => is_singular();
        $isArchive                   = fn() => is_archive() || is_home();
        $hasSchemaType               = fn() => $this->getCurrentPostSchemaType() !== null;
        $schemaType                  = fn() => $this->getCurrentPostSchemaType();
        $templateController          = fn() => ControllerHelper::camelCase($template);
        $templateControllerPath      = fn() => ControllerHelper::locateController($templateController());
        $templateControllerNamespace = fn() => ControllerHelper::getNamespace($templateControllerPath()) . '\\';
        $shouldUseSchemaController   = fn() =>  $hasSchemaType() &&
                                                $isSingular() &&
                                                class_exists("Municipio\Controller\Singular{$schemaType()}") &&
                                                (bool)ControllerHelper::locateController("Singular{$schemaType()}");

        $controllers = [
            [
                'condition'       => ('404' === $template),
                'controllerClass' => \Municipio\Controller\E404::class,
                'controllerPath'  => ControllerHelper::locateController('E404'),
            ],
            [
                'condition'       => $shouldUseSchemaController(),
                'controllerClass' => "Municipio\Controller\Singular{$schemaType()}",
                'controllerPath'  => ControllerHelper::locateController("Singular{$schemaType()}")
            ],
            [
                // If a controller for this specific WordPress template exists, use it.
                // @see https://developer.wordpress.org/themes/basics/template-hierarchy/ or naming conventions
                'condition'       => (bool) $templateControllerPath(),
                'controllerClass' => $templateControllerNamespace() . $templateController(),
                'controllerPath'  => $templateControllerPath(),
            ],
            [
                'condition'       => $isSingular(),
                'controllerClass' => \Municipio\Controller\Singular::class,
                'controllerPath'  => ControllerHelper::locateController('Singular')
            ],
            [
                'condition'       => $isArchive(),
                'controllerClass' => \Municipio\Controller\Archive::class,
                'controllerPath'  => ControllerHelper::locateController('Archive')
            ],
            [
                'condition'       => true,
                'controllerClass' => \Municipio\Controller\BaseController::class,
                'controllerPath'  => ControllerHelper::locateController('BaseController'),
            ]
        ];

        foreach ($controllers as $controller) {
            if ((bool) $controller['condition']) {
                $instance = $this->createController($controller, $template);
                if (!empty($controller['view'])) {
                    $template = $controller['view'];
                } elseif (!empty($instance->view)) {
                    $template = $instance->view;
                }

                return [
                    'data' => $instance,
                    'view' => $template
                ];
            }
        }

        return [];
    }

    /**
     * Get the current post schema type
     *
     * @return string|null The schema type of the current post. Null if not found.
     */
    public function getCurrentPostSchemaType(): ?string
    {
        return $this->schemaDataConfig->tryGetSchemaTypeFromPostType($this->wpService->getPostType());
    }
    /**
     * It loads a controller class and returns an instance of it
     *
     * @param array c An array containing the controller class and path.
     * @param string template The template name
     *
     * @return object An object of the controller class.
     */
    private function createController(array $c, string $template = ''): ?object
    {
        if (!isset($c['controllerPath']) || !is_file($c['controllerPath'])) {
            return null;
        }
        require_once apply_filters('Municipio/blade/controller', $c['controllerPath']);

        do_action_deprecated(
            'Municipio/blade/after_load_controller',
            $template,
            '3.0',
            'Municipio/blade/afterLoadController'
        );
        return new $c['controllerClass']($this->menuBuilder, $this->menuDirector, $this->wpService, $this->acfService);
    }
    /**
     * @param $view
     * @param array $data
     */
    public function renderView($view, $data = array())
    {
        try {
            $markup = $this->bladeEngine
                ->makeView($view, array_merge($data, array('errorMessage' => false)), [], $this->viewPaths)
                ->render();

            // Adds the option to make html more readable.
            // This is a option that is intended for permanent
            // use. But cannot be implemented due to some html
            // issues.
            if (class_exists('tidy') && isset($_GET['tidy'])) {
                $tidy = new \tidy();

                $tidy->parseString($markup, [
                    'indent'       => true,
                    'output-xhtml' => false,
                    'wrap'         => PHP_INT_MAX
                ], 'utf8');

                $tidy->cleanRepair();

                echo $tidy;
            } else {
                echo $markup;
            }
        } catch (\Throwable $e) {
            $this->bladeEngine->errorHandler($e)->print();
        }

        return false;
    }

    /**
     * Get a controller name
     * @param string $view The view path
     * @return void
     */
    private function getControllerNameFromView(string $view = ''): string
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
            'singular'   => 'singular.blade.php',
            'attachment' => 'attachment.blade.php',
        );

        $types = apply_filters_deprecated(
            'Municipio/blade/template_types',
            [$types],
            '3.0',
            'Municipio/blade/templateTypes'
        );

        if (isset($types) && !empty($types) && is_array($types)) {
            foreach ($types as $key => $type) {
                add_filter($key . '_template', function ($original) use ($key, $type, $types) {
                    // Front page
                    if (empty($original) && is_front_page()) {
                        $type = $types['front-page'];
                    }

                    // Template slug
                    if (get_queried_object() && get_page_template_slug()) {
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

                    // Look for post with schema type single page
                    $schemaType = $this->getCurrentPostSchemaType();
                    if (is_single() && !empty($schemaType)) {
                        $search = 'single-schema-' . strtolower($schemaType) . '.blade.php';
                        if ($found = \Municipio\Helper\Template::locateTemplate($search)) {
                            $templatePath = $found;
                        }
                    }

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
        $property   = $reflection->getProperty($prop);
        $property->setAccessible(true);
        return $property->getValue($obj);
    }

    /**
     * Cleans the view path by removing the base path and file extension.
     *
     * @param string $view The view path to be cleaned.
     * @return string The cleaned view path.
     */
    public function cleanViewPath($view)
    {
        $viewPaths = \Municipio\Helper\Template::getViewPaths();
        foreach ($viewPaths as $path) {
            $view = str_replace($path . '/', '', $view);
        }

        $view = str_replace('.blade.php', '', $view);
        return $view;
    }

    /**
     * Get Viewpaths and Blade engine runtime.
     *
     * @return void
     */
    public function initializeBlade()
    {
        $this->viewPaths   = $this->registerViewPaths();
        $componentLibrary  = new Init([]);
        $this->bladeEngine = $componentLibrary->getEngine();
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
    public function switchPageTemplate(string $view): string
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

        return $view;
    }

    /**
     * Register paths where views may be added
     * @return void
     * @throws \Exception
     */
    public function registerViewPaths(): array
    {
        $viewPaths = \Municipio\Helper\Template::getViewPaths();

        if (is_array($viewPaths) && !empty($viewPaths)) {
            return $viewPaths;
        }

        throw new \Exception("No view paths registered, please register at least one.");
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
        $view = strtolower(trim(str_replace(".blade.php", "", $view), "/"));

        return str_replace("/", ".", $view);
    }
}
