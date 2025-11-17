<?php

namespace Municipio;

use WpService\WpService;
use AcfService\AcfService;
use ComponentLibrary\Init;
use HelsingborgStad\BladeService\BladeServiceInterface;
use Municipio\Admin\Private\MainQueryUserGroupRestriction;
use Municipio\Controller\Navigation\MenuBuilderInterface;
use Municipio\Controller\Navigation\MenuDirector;
use Municipio\Helper\Controller as ControllerHelper;
use Municipio\Helper\Template as TemplateHelper;
use Municipio\Helper\SiteSwitcher\SiteSwitcher;
use Municipio\PostObject\Factory\PostObjectFromWpPostFactoryInterface;
use WP_Post;
use WP_Post_Type;
use Municipio\Helper\User\User;
use Municipio\SchemaData\Config\Contracts\TryGetSchemaTypeFromPostType;

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
     */
    public function __construct(
        private MenuBuilderInterface $menuBuilder,
        private MenuDirector $menuDirector,
        private AcfService $acfService,
        private WpService $wpService,
        private MainQueryUserGroupRestriction $mainQueryUserGroupRestriction,
        private SiteSwitcher $siteSwitcher,
        private PostObjectFromWpPostFactoryInterface $postObjectFromWpPost,
        private User $userHelper,
        private TryGetSchemaTypeFromPostType $tryGetSchemaTypeFromPostType
    ) {
        //Init custom templates & views
        add_action('template_redirect', array($this, 'registerViewPaths'), 10);
        add_action('template_redirect', array($this, 'initCustomTemplates'), 10);

        // Initialize blade
        add_action('template_redirect', array($this, 'initializeBlade'), 12);

        //Loads custom controllers and views
        add_action('template_redirect', array($this, 'addTemplateFilters'), 10);

        add_filter('template_include', array($this, 'switchPageTemplate'), 5);
        add_filter('template_include', array($this, 'sanitizeViewName'), 10);

        add_filter('template_include', array($this, 'loadViewData'), 15);
    }

    /**
     * Resolves nested templates by recursively processing them.
     *
     * @param string $template The template to process.
     * @param array $data The data to pass to the template.
     *
     * @return string The rendered template.
     */
    private function resolveNestedTemplates(string $template, array $data = []): string
    {
        // Check if the rendered template contains another template tag
        if (preg_match('/{{\s*template\((.*?)\)\s*}}/', $template, $matches)) {
            $nestedTemplate = trim($matches[1], "'\"");

            // Recursively resolve the nested template
            $nestedRendered = $this->resolveNestedTemplates($nestedTemplate, $data);

            // Replace the nested template tag with its rendered content
            $template = str_replace($matches[0], $nestedRendered, $template);
        }

        return $template;
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

        return $this->renderView($view, $viewData, [
            [$this, 'resolveNestedTemplates']
        ]);
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
        global $wp_query;

        // Bypass access restriction logic for generic static endpoints
        if (!$this->mayBeCustomTemplateRequest()) {
            if (
                !is_post_publicly_viewable() && !is_user_logged_in() && !is_search() && !is_archive() ||
                $this->mainQueryUserGroupRestriction->shouldRestrict($this->wpService->getQueriedObjectId())
            ) {
                if ($wp_query->found_posts > 0) {
                    if (!is_user_logged_in()) {
                        $template = '401';
                    } else {
                        $template = '403';
                    }
                } else {
                    $template = '404';
                }
            }

            if ($this->isPageForPostType() && !$this->isPageForPostTypePubliclyViewable() && !is_user_logged_in()) {
                $template = '401';
            }

            // Restrict access to single posts that belong to a post type with an assigned "page for post type"
            // if that page is not publicly viewable and the user is not logged in.
            if (
                $this->singlePostHasPostTypeThatUsesPageForPostType() &&
                !$this->isPostTypePubliclyViewable() &&
                !is_user_logged_in()
            ) {
                $template = '401';
            }
        }

        //Do something before controller creation
        do_action_deprecated(
            'Municipio/blade/before_load_controller',
            $template,
            '3.0',
            'Municipio/blade/beforeLoadController'
        );

        // Controller conditions
        $isSingular                       = fn() => is_singular();
        $isArchive                        = fn() => is_archive() || is_home();
        $hasSchemaType                    = fn() => $this->getCurrentPostSchemaType() !== null;
        $schemaType                       = fn() => $this->getCurrentPostSchemaType();
        $templateController               = fn() => ControllerHelper::camelCase($template);
        $templateControllerPath           = fn() => ControllerHelper::locateController($templateController());
        $templateControllerNamespace      = fn() => ControllerHelper::getNamespace($templateControllerPath()) . '\\';
        $shouldUseSchemaController        = fn() =>  $hasSchemaType() &&
                                                $isSingular() &&
                                                class_exists("Municipio\Controller\Singular{$schemaType()}") &&
                                                (bool)ControllerHelper::locateController("Singular{$schemaType()}");
        $shouldUseSchemaArchiveController = fn() =>  $isArchive() && $hasSchemaType() &&
                                                class_exists("Municipio\Controller\ArchiveSchema{$schemaType()}") &&
                                                (bool)ControllerHelper::locateController("ArchiveSchema{$schemaType()}");

        $controllers = [
            [
                'condition'       => ('404' === $template),
                'controllerClass' => \Municipio\Controller\E404::class,
                'controllerPath'  => ControllerHelper::locateController('E404'),
            ],
            [
                'condition'       => ('403' === $template),
                'controllerClass' => \Municipio\Controller\E403::class,
                'controllerPath'  => ControllerHelper::locateController('E403'),
            ],
            [
                'condition'       => ('401' === $template),
                'controllerClass' => \Municipio\Controller\E401::class,
                'controllerPath'  => ControllerHelper::locateController('E401'),
            ],
            [
                'condition'       => $shouldUseSchemaArchiveController(),
                'controllerClass' => "Municipio\Controller\ArchiveSchema{$schemaType()}",
                'controllerPath'  => ControllerHelper::locateController("ArchiveSchema{$schemaType()}")
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
     * Determines if the current single post has a post type that uses a "page for post type" assignment.
     *
     * @return bool
     */
    private function singlePostHasPostTypeThatUsesPageForPostType(): bool
    {
        if (!is_singular() || !is_main_query()) {
            return false;
        }

        $queriedObject = $this->wpService->getQueriedObject();

        if (!$queriedObject instanceof WP_Post) {
            return false;
        }

        return $this->hasPageForPostType($queriedObject->post_type);
    }

    /**
     * Checks if the current post type is publicly viewable.
     *
     * This method checks if the queried object is a post and if its post type has a page assigned.
     * If a page is assigned, it checks if that page is publicly viewable.
     *
     * @return bool True if the post type is publicly viewable, false otherwise.
     */
    private function isPostTypePubliclyViewable(): bool
    {
        $queriedObject = $this->wpService->getQueriedObject();

        if (!$queriedObject instanceof WP_Post) {
            return true;
        }

        $postType = $queriedObject->post_type;

        if (!post_type_exists($postType)) {
            return true;
        }

        $pageForPostTypeId = $this->getPageForPostTypePageIdFromPostType($postType);

        if ($pageForPostTypeId === null) {
            return true;
        }

        return is_post_publicly_viewable($pageForPostTypeId);
    }

    /**
     * Checks if a page is assigned for the given post type.
     *
     * @param string $postType
     * @return bool
     */
    private function hasPageForPostType(string $postType): bool
    {
        return $this->getPageForPostTypePageIdFromPostType($postType) !== null;
    }

    /**
     * Retrieves the page ID assigned to a post type archive, if any.
     *
     * @param string $postType
     * @return int|null
     */
    private function getPageForPostTypePageIdFromPostType(string $postType): ?int
    {
        $pageId = get_option('page_for_' . $postType);

        if (is_numeric($pageId) && (int)$pageId > 0) {
            return (int)$pageId;
        }

        return null;
    }

    /**
     * Check if the current archive is assigned to a page for a post type.
     *
     * @return bool
     */
    private function isPageForPostType(): bool
    {
        return $this->getPageForPostTypePageId() !== null;
    }

    /**
     * Check if the page assigned to a post type archive is publicly viewable by the current user.
     *
     * @param int|null $pageId The page ID to check. If null, it will attempt to get the page ID for the current post type archive.
     *
     * @return bool
     */
    private function isPageForPostTypePubliclyViewable(?int $pageId = null): bool
    {
        $pageId = $pageId === null ? $this->getPageForPostTypePageId() : $pageId;

        if ($pageId === null) {
            return true;
        }

        return is_post_publicly_viewable($pageId);
    }

    /**
     * Get the page ID assigned to a post type archive, if any.
     *
     * @return int|null The page ID or null if not found.
     */
    private function getPageForPostTypePageId(): ?int
    {
        if (!is_archive()) {
            return null;
        }

        $queriedObject = $this->wpService->getQueriedObject();

        if (
            !$queriedObject instanceof WP_Post_Type ||
            !post_type_exists($queriedObject->name)
        ) {
            return null;
        }

        $pageId = get_option('page_for_' . $queriedObject->name);

        return (is_numeric($pageId) && (int)$pageId > 0) ? (int)$pageId : null;
    }

    /**
     * Get the current post schema type
     *
     * @return string|null The schema type of the current post. Null if not found.
     */
    public function getCurrentPostSchemaType(): ?string
    {
        global $post;

        if (empty($post)) {
            return null;
        }

        return $this->tryGetSchemaTypeFromPostType->tryGetSchemaTypeFromPostType($post->post_type);
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

        return new $c['controllerClass'](
            $this->menuBuilder,
            $this->menuDirector,
            $this->wpService,
            $this->acfService,
            $this->siteSwitcher,
            $this->userHelper
        );
    }
    /**
     * @param $view
     * @param array $data
     */
    public function renderView($view, $data = [], ?array $additionalParsers)
    {
        // Ensure blade engine is initialized before rendering
        if ($this->bladeEngine === null) {
            $this->initializeBlade();
        }

        try {
            $markup = $this->bladeEngine
                ->makeView($view, array_merge($data, ['errorMessage' => false]), [], $this->viewPaths)
                ->render();

            // Hookable filter to get all markup output (Used by WPMUSecurity)
            $this->wpService->applyFilters('Website/HTML/output', $markup);

            // Adds the option to make html more readable and fixes some validation issues (like /> in void elements)
            if (class_exists('tidy') && (!defined('DISABLE_HTML_TIDY') || constant('DISABLE_HTML_TIDY') !== true)) {

                //Leave out <template> blocks from tidy processing
                $templates = [];
                $markup = preg_replace_callback(
                    '/<template\b[^>]*>.*?<\/template>/is',
                    function ($matches) use (&$templates) {
                        $key = '__TEMPLATE_PLACEHOLDER_' . count($templates) . '__';
                        $templates[$key] = $matches[0];
                        return $key;
                    },
                    $markup
                );

                $tidy = new \tidy();
                $tidy->parseString($markup, [
                    'indent'              => true,
                    'output-xhtml'        => false,
                    'wrap'                => PHP_INT_MAX,
                    'doctype'             => 'html5',
                    'drop-empty-elements' => false,
                    'drop-empty-paras'    => false
                ], 'utf8');

                // Clean and repair the document
                $tidy->cleanRepair();
                $markup = (string) $tidy;

                // Restore <template> blocks
                $markup = str_replace(array_keys($templates), array_values($templates), $markup);

                // Minify inline <style> content
                $markup = preg_replace_callback(
                    '/<style(?:\s+[^>]*)?>(.*?)<\/style>/is',
                    fn($m) => '<style>' . $this->minifyCss($m[1]) . '</style>',
                    $markup
                );

                // Minify inline <script> content
                $markup = preg_replace_callback(
                    '/<script\b([^>]*)>(.*?)<\/script>/is',
                    fn($m) => '<script' . $m[1] . '>' . $this->minifyJs($m[2]) . '</script>',
                    $markup
                );

                // Drop comments
                if (!defined('WP_DEBUG') || (defined('WP_DEBUG') && constant('WP_DEBUG') !== true)) {
                    $markup = preg_replace('/<!--(.|\s)*?-->/', '', $markup);
                }

                // Drop empty id attributes
                $markup = preg_replace('/\sid=""/', '', $markup);

                //Drop attibute that no longer is to spec
                $markup = $this->dropPropertyAttributes([
                    'style'  => ['type' => 'text/css'],
                    'script' => ['type' => 'text/javascript'],
                ], $markup);

            }

            //Run additional parsers if any
            foreach($additionalParsers as $parser) {
                if (is_callable($parser)) {
                    $parser($markup);
                }
            }

            echo $markup;

        } catch (\Throwable $e) {
            $this->bladeEngine->errorHandler($e)->print();
        }

        return false;
    }

    /**
     * Minify CSS by removing excessive whitespace
     *
     * @param string $css The CSS to minify
     *
     * @return string The minified CSS
     */
    private function minifyCss(string $css): string
    {
        if (defined('MUNIPIO_DISABLE_CSS_MINIFY') && constant('MUNIPIO_DISABLE_CSS_MINIFY') === true) {
            return $css;
        }
        return preg_replace(['/\/\*.*?\*\//s', '/\s+/'], ['', ' '], trim($css));
    }

    /**
     * Minify JS by removing excessive whitespace
     *
     * @param string $js The JS to minify
     *
     * @return string The minified JS
     */
    private function minifyJs(string $js): string
    {
        if (defined('MUNIPIO_DISABLE_JS_MINIFY') && constant('MUNIPIO_DISABLE_JS_MINIFY') === true) {
            return $js;
        }

        $js = preg_replace('/^[ \t]*\/\/.*$/m', '', $js); // Remove single line comments
        return preg_replace(['/\/\*.*?\*\//s', '/\s+/'], ['', ' '], trim($js)); // Minify
    }

    /**
     * Drop attributes from HTML tags
     *
     * @param array $dropAttributesConfig The configuration for what attributes to remove
     * @param string $cleanedHtml The HTML to clean
     *
     * @return string The cleaned HTML
     */
    private function dropPropertyAttributes(array $dropAttributesConfig, string $cleanedHtml): string
    {
        foreach ($dropAttributesConfig as $tag => $attributes) {
            foreach ($attributes as $attribute => $value) {
                // Create the pattern to match the attribute with the specified value
                $pattern = '/<' . $tag . '\s+([^>]*\s*)' . $attribute . '=["\']' . preg_quote($value, '/') . '["\']([^>]*)>/is';

                // Replace the matched tag by removing the specified attribute
                $cleanedHtml = preg_replace_callback($pattern, function ($matches) use ($tag) {
                    // Rebuild the tag without the specified attribute
                    return '<' . $tag . ' ' . $matches[1] . $matches[2] . '>';
                }, $cleanedHtml);
            }
        }

        return $cleanedHtml;
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
            '403'        => '403.blade.php',
            '401'        => '401.blade.php',
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
        // Prevent multiple initializations
        if ($this->bladeEngine !== null) {
            return;
        }

        $this->viewPaths   = $this->registerViewPaths();
        $componentLibrary  = new Init($this->viewPaths);
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
     * Check if the current request is for a custom template by running a filter
     *
     * @return bool True if it's a custom template request, false otherwise.
     */
    private function mayBeCustomTemplateRequest(): bool
    {
        return apply_filters(
            'Municipio/Template/MayBeCustomTemplateRequest',
            false
        );
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
