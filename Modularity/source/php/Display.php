<?php

namespace Modularity;

use Throwable;
use ComponentLibrary\Init as ComponentLibraryInit;
use \Modularity\Helper\File as FileHelper;
use Modularity\Helper\Wp;
use WP_Post;

class Display
{
    /**
     * Holds the current post's/page's modules
     * @var array
     */
    public $modules = array();
    public $options = null;
    private $isBlock = false;
    private $isRenderingModule = false;
    private static $renderedShortcodeModules = [];
    private $isShortcode = false; // Flag to indicate if the current context is a shortcode.
    private static $sidebarState = []; //Holds state of sidebars.

    public function __construct()
    {
        add_filter('wp', array($this, 'init'));
        add_filter('is_active_sidebar', array($this, 'isActiveSidebar'), 10, 2);

        add_shortcode('modularity', array($this, 'shortcodeDisplay'));
        add_filter('the_post', array($this, 'filterNestedModuleShortocde'));

        add_filter('Modularity/Display/Markup', array($this, 'addGridToSidebar'), 10, 2);

        add_filter('acf/format_value/type=wysiwyg', array( $this, 'filterModularityShortcodes'), 9, 3);
        add_filter('Modularity/Display/SanitizeContent', array($this, 'sanitizeContent'), 10);
        add_filter('Modularity/Display/replaceGrid', array($this, 'replaceGridClasses'), 10);

        add_filter('ComponentLibrary/Component/Data', function ($data) {
            if ($this->isRenderingModule) {
                $data['isBlock'] = $this->isBlock;
            }

            if ($this->isShortcode) {
                $data['isShortcode'] = true;
            }

            return $data;
        });
    }

    /**
     * Replace legacy grid class names with the new format.
     *
     * This function takes a CSS class name as input and replaces legacy grid class
     * names (e.g., 'grid-md-12', 'grid-md-9') with the corresponding new format
     * (e.g., 'o-grid-12@md', 'o-grid-9@md'). This is useful for updating class names
     * in HTML markup to align with a new naming convention.
     *
     * @param string $className The CSS class name to be processed.
     *
     * @return string The modified CSS class name with legacy grid class names replaced.
     */
    public function replaceGridClasses($className)
    {
        return preg_replace(
            '/grid-md-(\d+)/', 
            'o-grid-$1@md', 
            $className
        );
    }

    /**
     * Get module directory by post-type
     * @param $postType
     * @return mixed|null
     */
    public function getModuleDirectory($postType)
    {
        if (!is_dir(MODULARITY_PATH . 'source/php/Module/')) {
            return null;
        }

        $directories = FileHelper::glob(
            MODULARITY_PATH . 'source/php/Module/*',
        ); 

        if(!empty($directories) && is_array($directories)) {
            foreach ($directories as $dir) {
                $pathinfo = pathinfo($dir);
                if (strtolower(str_replace('mod-', '', $postType)) === strtolower($pathinfo['filename'])) {
                    return $pathinfo['filename'];
                }
            }
        }

        return null;
    }

    /**
     * @param $view
     * @param array $data
     * @return bool
     * @throws \Exception
     * 
     * TODO: This needs to be checked if it is optimizable. 
     *       An component library init for each render can not be grate.  
     */
    public function renderView($view, $data = array()): string
    {
        $data['sidebarContext'] = \Modularity\Helper\Context::get();

        $this->isBlock = !empty($data['blockData']);
        $this->isRenderingModule = true;

        // Adding Module path to filter
        $moduleView = MODULARITY_PATH . 'source/php/Module/' . $this->getModuleDirectory($data['post_type']) . '/views';
        $externalViewPaths = apply_filters('/Modularity/externalViewPath', []);

        if (isset($externalViewPaths[$data['post_type']])) {
            $moduleView = $externalViewPaths[$data['post_type']];
        }

        $init = new ComponentLibraryInit([]);
        $blade = $init->getEngine();

        $filters = [
            fn ($d) => apply_filters('Modularity/Display/viewData', $d),
            fn ($d) => apply_filters("Modularity/Display/{$d['post_type']}/viewData", $d),
        ];

        $viewData = array_reduce(
            $filters,
            fn (array $d, callable $applyFilter) => $applyFilter($d),
            $data
        );

        try {
            $rendered = $blade->makeView( $view, $viewData, [], $moduleView )->render();
            $this->isRenderingModule = false;

            return $rendered;
        } catch (Throwable $e) {
            $blade->errorHandler($e)->print();
        }

        $this->isRenderingModule = false;
        return false;
    }

    /**
     * Removes modularity shortcodes wysiwyg fields to avoid infinity loops
     * @param mixed $value  The value which was loaded from the database
     * @param mixed $postId The post ID from which the value was loaded
     * @param array $field  An array containing all the field settings for the field which was used to upload the attachment
     * @return mixed
     */
    public function filterModularityShortcodes($value, $postId, $field)
    {
        return preg_replace(
            '/\[modularity(.*)\]/', 
            '', 
            $value
        );
    }

    /**
     * Removes modularity shortcodes from post content field to avoid infinity loops
     * @param string  $content  The post content
     * @param int     $postId   The post content
     * @return string
     */
    public function sanitizeContent($content)
    {
        return preg_replace(
            '/\[modularity(.*)\]/', 
            '', 
            $content)
        ;
    }

    /**
     * Check if modules are active for a sidebar.
     *
     * @param string $sidebar Sidebar id
     * @return boolean
     */
    private function areModulesActive($sidebar)
    {
        if (isset($this->modules[$sidebar]) && count($this->modules[$sidebar]) > 0) {
            foreach ($this->modules[$sidebar]['modules'] as $module) {
                if (!is_preview() && $module->hidden == 'true') {
                    continue;
                }

                return true;
            }
        }
        return false;
    }

    /**
     * Check if widgets are active for a sidebar.
     *
     * @param string $sidebar Sidebar id
     * @return boolean
     */
    private function areWidgetsActive($sidebar)
    {
        $widgets = wp_get_sidebars_widgets();
        if (is_null($widgets) || empty($widgets) || !isset($widgets[$sidebar])) {
            return false;
        }
        
        $widgets = array_map('array_filter', $widgets);

        return !empty($widgets[$sidebar]);
    }

    /**
     * New is_active_sidebar logic which includes module and widget checks.
     *
     * @param boolean $isActiveSidebar Original response
     * @param string $sidebar Sidebar id
     * @return boolean
     */
    public function isActiveSidebar($isActiveSidebar, $sidebar)
    {
        // Just figure out the state of a sidebar once
        if (isset(self::$sidebarState[$sidebar])) {
            return self::$sidebarState[$sidebar];
        }

        $hasWidgets = $this->areWidgetsActive($sidebar);
        $hasModules = $this->areModulesActive($sidebar);

        if ($hasWidgets || $hasModules) {
            return self::$sidebarState[$sidebar] = true;
        }

        return self::$sidebarState[$sidebar] = false;
    }


    /**
     * Initialize, get post's/page's modules and start output
     * @return void
     */
    public function init()
    {
        global $post;
        global $wp_query;

        if (!$wp_query->is_main_query() || empty($post)) {
            return;
        }

        $realPostID = $this->getCurrentPostID($post);

        if (is_admin() || is_feed() || is_tax() || post_password_required($realPostID) || is_404()) {
            return;
        }

        $archiveSlug = \Modularity\Helper\Wp::getArchiveSlug();

        if (isset($wp_query->query['modularity_template']) && !empty($wp_query->query['modularity_template'])) {
            $this->modules = \Modularity\Editor::getPostModules($wp_query->query['modularity_template']);
            $this->options = get_option('modularity_' . $wp_query->query['modularity_template'] . '_sidebar-options');
        } elseif ($archiveSlug) {
            $this->modules = \Modularity\Editor::getPostModules($archiveSlug);
            $this->options = get_option('modularity_' . $archiveSlug . '_sidebar-options');
        } elseif ($realPostID) {
            $this->modules = \Modularity\Editor::getPostModules($realPostID);
            $this->options = get_option('modularity-sidebar-options');
            
            $postTypeModules = $this->setupModulesForSingle();
            
            if( !empty($postTypeModules) ) {
                $this->modules = $this->mergeModules($this->modules, $postTypeModules);
            }
        }

        add_action('dynamic_sidebar_before', array($this, 'outputBefore'));
        add_action('dynamic_sidebar_after', array($this, 'outputAfter'));

        add_filter('sidebars_widgets', array($this, 'hideWidgets'));
    }

    private function getCurrentPostID($post) {
        if (defined('PAGE_FOR_POSTTYPE_ID') && is_numeric(PAGE_FOR_POSTTYPE_ID)) {
            return PAGE_FOR_POSTTYPE_ID;
        } else {
            return $post->ID;
        }
    }

    private function setupModulesForSingle():array
    {
        $modules = [];
        $singleSlug = Wp::getSingleSlug();
        
        if ($singleSlug) {
            $modules = \Modularity\Editor::getPostModules($singleSlug);
            $modules = !is_array($modules) ? [] : $modules;
        }

        return $modules;
    }

    private function mergeModules($first, $second): array
    {
        $merged = [];
        $sidebars = array_merge(array_keys($first), array_keys($second));

        foreach ($sidebars as $sidebar) {
            if (isset($first[$sidebar]) && isset($second[$sidebar])) {
                $merged[$sidebar] = ['modules' => array_merge($second[$sidebar]['modules'], $first[$sidebar]['modules'])];
            } else if (isset($first[$sidebar])) {
                $merged[$sidebar] = $first[$sidebar];
            } else if (isset($second[$sidebar])) {
                $merged[$sidebar] = $second[$sidebar];
            }
        }

        return $merged;
    }

    /**
     * Unsets (hides) widgets from sidebar if set in Modularity options
     * @param  array $sidebars Sidebars and widgets
     * @return array           Filtered sidebars and widgets
     */
    public function hideWidgets($sidebars)
    {
        $retSidebars = $sidebars;

        foreach ($retSidebars as $sidebar => $widgets) {
            if (!empty($retSidebars[$sidebar]) && (!isset($this->options[$sidebar]['hide_widgets']) || $this->options[$sidebar]['hide_widgets'] != 'true')) {
                continue;
            }

            $retSidebars[$sidebar] = array('');
        }

        return $retSidebars;
    }

    /**
     * Get sidebar arguments of a specific sidebar id
     * @param  string $id        The sidebar id to look for
     * @return boolean/array     false if nothing found, else the arguments in array
     */
    public function getSidebarArgs($id)
    {
        global $wp_registered_sidebars;

        if (!isset($wp_registered_sidebars[$id])) {
            return false;
        }

        return $wp_registered_sidebars[$id];
    }

    /**
     * Check if modules should be outputted before widgets
     * @param  string $sidebar Current sidebar
     * @return boolean|void
     */
    public function outputBefore($sidebar)
    {
        if (!isset($this->options[$sidebar]['hook']) || $this->options[$sidebar]['hook'] != 'before') {
            return false;
        }

        $this->output($sidebar);
    }

    /**
     * Check if modules should be outputted after widgets
     * @param  string $sidebar Current sidebar
     * @return boolean|void
     */
    public function outputAfter($sidebar)
    {
        if (isset($this->options[$sidebar]['hook']) && $this->options[$sidebar]['hook'] != 'after') {
            return false;
        }

        $this->output($sidebar);
    }

    /**
     * Outputs the modules of a specific sidebar
     * @param  string $sidebar Sidebar id/slug
     * @return void
     */
    public function output($sidebar)
    {
        if (!isset($this->modules[$sidebar])) {
            return;
        }

        // Get modules
        $modules = $this->modules[$sidebar];

        // Get sidebar arguments
        $sidebarArgs = $this->getSidebarArgs($sidebar);

        // Update context
        if (isset($sidebarArgs['id'])) {
            \Modularity\Helper\Context::set(
                "sidebar." . $sidebarArgs['id']
            );
        }

        // Loop and output modules
        if (isset($modules['modules']) && is_array($modules['modules']) && !empty($modules['modules'])) {
            foreach ($modules['modules'] as $module) {
                
                if(!$this->shouldDisplayModule($module)) {
                    continue;
                }

                $this->outputModule(
                    $module, 
                    $sidebarArgs, 
                    \Modularity\ModuleManager::$moduleSettings[
                        get_post_type($module)
                    ]
                );
            }
        }

        //Reset context
        if (isset($sidebarArgs['id'])) {
            \Modularity\Helper\Context::set(false);
        }
    }

    /**
     * Determine whether a module should be displayed.
     *
     * This function checks if a module should be displayed based on certain conditions.
     *
     * @param mixed $module The module to be evaluated.
     *
     * @return bool Returns `true` if the module should be displayed, and `false` otherwise.
     */
    private function shouldDisplayModule($module) {
        if (!is_preview() && $module->hidden == 'true') {
            return false;
        }
        return true;
    }

    /**
     * Outputs a specific module
     * @param  object $module           The module data
     * @param  array $args              The sidebar data
     * @param  array $moduleSettings    The module configuration
     * @return boolean                  True if success otherwise false
     * 
     * TODO: Return method needs the ability to be cached.
     */
    public function outputModule($module, $args = array(), $moduleSettings = array(), $echo = true)
    {
        if (!$module instanceof \WP_Post) {
            return false;
        }

        if (!isset($args['id'])) {
            $args['id'] = 'no-id';
        }

        //Do not cache private modules
        if(get_post_status($module) === 'private') {
            $moduleSettings['cache_ttl'] = 0;
        }

        $cache = new \Modularity\Helper\Cache(
            $module->ID, [
                $module, 
                $args['id']
            ], 
            $moduleSettings['cache_ttl'] ?? 0,
            $this->getAllAllowedAndRegisteredQueryVars() ?: null
        );

        if ($echo == false) {
            $class = \Modularity\ModuleManager::$classes[$module->post_type];
            $module = new $class($module, $args);

            return $this->getModuleMarkup($module, $args);
        }

        if ($cache->start()) {

            $class = \Modularity\ModuleManager::$classes[$module->post_type];
            $module = new $class($module, $args);

            //Print module
            echo $this->getModuleMarkup(
                $module,
                $args
            );

            $cache->stop(); //Stop cache
        }

        return true;
    }

    /**
     * Get all allowed and registered query vars
     * 
     * This function retrieves all registered and allowed query variables,
     * merging them into a single array while filtering out any empty values.
     *
     * @return array An array of all allowed and registered query variables.
     */
    private function getAllAllowedAndRegisteredQueryVars(): array
    {
        $registeredQueryVars = $this->getRegisteredQueryVars();
        $allowedQueryVars = $this->getAllowedQueryVars();

        // Merge and filter out empty values
        return array_filter(
            array_merge($registeredQueryVars, $allowedQueryVars)
        );
    }

    /**
     * Get registered query vars
     * @return array
     */
    private function getRegisteredQueryVars(): array
    {
        global $wp;
        $result = [];
        if (isset($wp->public_query_vars) && is_array($wp->public_query_vars)) {
            foreach ($wp->public_query_vars as $var) {
                $result[$var] = get_query_var($var);
            }
        }
        return array_filter($result);
    }

    /**
     * Get allowed query vars from $_GET
     * 
     * This function retrieves query variables from the global $_GET array that match a specific pattern.
     * The pattern is defined to match query variables that start with 'mod-' followed by alphanumeric characters,
     * dashes, or underscores.
     *
     * @return array An array of allowed query variable names.
     */
    private function getAllowedQueryVars(): array
    {
        $pattern = '/^mod-([a-z0-9\-_]+)$/i';
        if($_GET ?? null) {
            $queryVars = array_filter(
                array_keys($_GET),
                function ($var) use ($pattern) {
                    return preg_match($pattern, $var);
                }
            );
            foreach ($queryVars as $key => $value) {
                $queryVars[$value] = sanitize_text_field($_GET[$value] ?? '');
            }
        } else {
            $queryVars = [];
        }
        return $queryVars;
    }

    /**
     * Gets markup for a module
     * @param  object $module The module object
     * @param  array  $args   Module args
     * @return string
     * 
     * TODO: Needs refactor, in order to clarify its purpose.
     */
    public function getModuleMarkup($module, $args)
    {
        $templatePath = $module->template();

        if (!$templatePath) {
            return false;
        }

        $moduleMarkup = $this->loadBladeTemplate(
            $templatePath,
            $module,
            $args
        );

        if (empty($moduleMarkup)) {
            return;
        }

        $classes = array(
            'modularity-' . $module->post_type,
            'modularity-' . $module->post_type . '-' . $module->ID,
            (property_exists($module, 'columnWidth')) ? $module->columnWidth :  'o-grid-12'
        );

        //Hide module if preview
        if (is_preview() && isset($module->hidden) && $module->hidden) {
            $classes[] = 'modularity-preview-hidden';
        }

        //Add selected scope class
        if (isset($module->data['meta']) && isset($module->data['meta']['module_css_scope']) &&
            is_array($module->data['meta']['module_css_scope'])) {
            if (!empty($module->data['meta']['module_css_scope'][0])) {
                $classes[] = $module->data['meta']['module_css_scope'][0];
            }
        }

        // Build before & after module markup
        $beforeModule = (array_key_exists('before_widget', $args)) ? $args['before_widget'] :
            '<div id="%1$s" class="%2$s" >';
        $afterModule = (array_key_exists('after_widget', $args)) ? $args['after_widget'] : '</div>';

        // Apply filter for classes
        $classes = (array) apply_filters('Modularity/Display/BeforeModule::classes', $classes, $args, $module->post_type, $module->ID);

        // Set id (%1$s) and classes (%2$s)
        $beforeModule = sprintf($beforeModule, $module->post_type . '-' . $module->ID . '-' . uniqid(), implode(' ', $classes));
        
        // Append module edit to before markup
        if ($this->displayEditModule($module, $args) && !is_admin()) {
            $beforeModule .= $this->createEditModuleMarkup($module);
        }

        // Apply filter for before/after markup
        $beforeModule = apply_filters(
            'Modularity/Display/BeforeModule', 
            $beforeModule, $args, $module->post_type, $module->ID
        );
        $afterModule = apply_filters(
            'Modularity/Display/AfterModule', 
            $afterModule, $args, $module->post_type, $module->ID
        );

        // Concat full module
        $moduleMarkup = $beforeModule . $moduleMarkup . $afterModule;

        //Add filters to output
        $moduleMarkup = apply_filters(
            'Modularity/Display/Markup', 
            $moduleMarkup, 
            $module
        );
        $moduleMarkup = apply_filters(
            'Modularity/Display/' . $module->post_type . '/Markup', 
            $moduleMarkup, 
            $module
        );

        return $moduleMarkup;
    }

    /**
     * Determine if the edit module button should be displayed.
     * 
     * @param  class  $module   Module class
     * @param  array    $args   Module argument array
     * @return bool             If the button should be displayed or not        
     *  
     */
    private function displayEditModule($module, $args) {
        
        if(isset($args['edit_module']) && $args['edit_module'] !== false) {
            return false;
        }
        
        if(wp_doing_ajax()) {
            return false;
        }

        if(defined('REST_REQUEST')) {
            return false;
        }

        if(!current_user_can('edit_module', $module->ID)) {
            return false;
        }

        return true; 
    }

    /**
     * Create and return markup for editing a module.
     *
     * This function generates HTML markup for editing a module. It creates a link to the WordPress
     * admin panel for editing the module with the specified parameters.
     *
     * @param WP_Post $module The module post object to edit.
     * @return string HTML markup for editing the module.
     * 
     * TODO: Needs filters
     */
    private function createEditModuleMarkup($module) {
        $options = get_option('modularity-options');
        $linkParameters = [
            'post' => $module->ID ,
            'action' => 'edit',
            'is_thickbox' => 'true',
            'is_inline' => 'true'
        ]; 

        if (isset($options['show-modules-usage-in-frontend']) && $options['show-modules-usage-in-frontend'] == 'on') {
            $usage = sizeof(ModuleManager::getModuleUsage($module->ID));
            if ($usage > 1) {
                $module->data['post_type_name'] .= ' (' . $usage . ')';
            }
        }

        return '
            <div class="modularity-edit-module">
                <a href="' . admin_url('post.php?' . http_build_query($linkParameters)) . '">
                    ' . __('Edit module', 'modularity') . ': ' . $module->data['post_type_name'] .  '
                </a>
            </div>
        ';
    }

    /**
     * Check if template exists and render the template
     * @param string $view View file
     * @param class $module Module class
     * @return string         Template markup
     * @throws \Exception
     */
    public function loadBladeTemplate($view, $module, array $args = array())
    {
        if (!$module->templateDir) {
            throw new \LogicException('Class ' . get_class($module) . ' must have property $templateDir');
        }

        return $this->renderView(
            \Modularity\Helper\Template::getModuleTemplate(
                $view,
                $module,
                true
            ),
            $module->data
        );
    }

    /**
     * Display module with shortcode
     * @param  array $args Args
     * @return string      Html markup
     * 
     * TODO: Needs to use more common code. There 
     *       are several usable functions to achive this. 
     */
    public function shortcodeDisplay($args)
    {
        $args = shortcode_atts(array('id' => false, 'inline' => true), $args);

        if (!is_numeric($args['id'])) {
            return;
        }

        if (isset(self::$renderedShortcodeModules[$args['id']])) {
            return self::$renderedShortcodeModules[$args['id']];
        }

        //Get module details
        $module = \Modularity\Editor::getModule($args['id']);

        //If not valid details, abort.
        if (!is_object($module) || empty($module->post_type)) {
            return "";
        }

        //Create instance
        $class = \Modularity\ModuleManager::$classes[$module->post_type];
        $module = new $class($module, $args);

        $this->isShortcode = true;
        $moduleMarkup = $this->getModuleMarkup($module, $args);
        if (empty($moduleMarkup)) {
            $this->isShortcode = false;
            return;
        }

        $moduleMarkup = apply_filters('Modularity/Display/Markup', $moduleMarkup, $module);
        $moduleMarkup = apply_filters('Modularity/Display/' . $module->post_type . '/Markup', $moduleMarkup, $module);
        $moduleMarkup = '<div class="' . $module->post_type . '">' . $moduleMarkup . '</div>';

        self::$renderedShortcodeModules[$args['id']] = $moduleMarkup;
        $this->isShortcode = false;
        return $moduleMarkup;
    }

    /**
     * Removes nested shortcodes
     * @param  WP_Post $post
     * @return WP_Post
     */
    public function filterNestedModuleShortocde($post)
    {
        if (is_admin()) {
            return $post;
        }

        if (substr($post->post_type, 0, 4) != 'mod-') {
            return $post;
        }

        $post->post_content = preg_replace('/\[modularity(.*)\]/', '', $post->post_content);
        return $post;
    }

    /**
     * Add container grid to specified modules, in specified sidebars
     * @param  $markup The module markup
     * @return $module Module object
     * 
     * TODO: Investigate if this is necessary.
     * 
     */

    public function addGridToSidebar($markup, $module)
    {
        $sidebars   = apply_filters('Modularity/Module/Container/Sidebars', array());
        $modules    = apply_filters('Modularity/Module/Container/Modules', array());
        $template   = apply_filters('Modularity/Module/Container/Template', '<div class="container"><div class="grid"><div class="grid-xs-12">{{module-markup}}</div></div></div>');

        if (!isset($module->args['id']) || !isset($module->post_type)) {
            return $markup;
        }

        if (in_array($module->args['id'], $sidebars) && in_array($module->post_type, $modules)) {
            return str_replace('{{module-markup}}', $markup, $template);
        }

        return $markup;
    }
}
