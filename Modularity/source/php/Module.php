<?php

namespace Modularity;

use Modularity\Helper\AcfService;
use Modularity\Helper\WpService;

class Module
{
    /**
     * WP_Post properties will automatically be extracted to properties of this class.
     * This array contains the keys to the extracted properties.
     * @var array
     */
    public $extractedPostProperties = array();

    /**
     * A place to store the id of a module instance.
     * Adds block support, which dosen't require a ID (works well with null).
     *
     * @var int|string|null
     */
    public $ID = null;

    public ?string $post_type = null;

    protected string $postStatus = '';

    /**
     * The slug of the module
     * Example: image
     * @var string
     */
    public $slug = '';
    public $moduleSlug = '';

    /**
     * Singular name of the modue
     * Example: Image
     * @var string
     */
    public $nameSingular = '';

    /**
     * Plural name of the module
     * Example: Images
     * @var string
     */
    public $namePlural = '';

    /**
     * Module description
     * Shows a fixed with and height image
     * @var string
     */
    public $description = '';

    /**
     * Module icon (Base64 endoced data uri)
     * @var string
     */
    public $icon = '';

    /**
     * What the module post type should support (title and revision will be added automatically)
     * Example: array('editor', 'attributes')
     * @var array
     */
    public $supports = array();

    /**
     * What the block block supports
     * Example: align (full width etc)
     * @var array
     */
    public $blockSupports = array();

    /**
     * If empty block notice should be used. 
     * @var bool
     */
    public $useEmptyBlockNotice = false; 

    /**
     * Any module plugins (path to file to include)
     * @var array
     */
    public $plugin = array();

    /**
     * Cache ttl
     * @var integer
     */
    public $cacheTtl = 3600 * 24 * 7;

    /**
     * The initial setting for "hide title" of the module
     * @var boolean
     */
    public $hideTitle  = false;

    /**
     * Sidebar arguments
     * @var array
     */
    public $args = array();

    /**
     * Is the module deprecated?
     * @var boolean
     */
    public $isDeprecated = false;

    /**
     * Will the module work as a block?
     * @var boolean
     */
    public $isBlockCompatible = true;

    /**
     * A field to replace the post title if module is used as a block.
     * @var boolean
     */
    public $expectsTitleField = true;

    /**
     * Is this module a legacy module (not updated to new registration methods)
     * @var boolean
     */
    public $isLegacy = false;

    /**
     * Set to tro if only available for multisites
     * @var boolean
     */
    public $multisiteOnly = false;

    /**
     * Path to template folder for this module
     * @var string
     */
    public $templateDir = false;

    /**
     * Path to assets folder for this module
     * @var string
     */
    public $assetDir = false;

    /**
     * View data (data that will be sent to the blade view)
     * @var array
     */
    public $data = array();

    /**
     * Module mode
     * @var string
     */
    public $mode = 'module'; //May be either 'module' or 'block'.

    /**
     * Data dataFetched.
     * Keeps track if the current data is fetched by the native data fetch functionality. 
     * @var string
     */
    public $dataFetched = false; //May be either 'module' or 'block'.

    /**
     * Column width.
     * Keeps track if the current size if the module.
     * @var string
     */
    public $columnWidth;

    /**
     * Constructs a module
     * Override the wpService and acfService to use a fake service for testing
     *
     * @param WP_Post $post - Core post object
     * @param array $args - Extra arguments
     */
    public function __construct(
        // Provided by WordPress
        ?\WP_Post $post = null,
        $args = array())
    {
        $this->args = $args;

        $this->ID = $post->ID ?? null;
        $this->mode = $this->ID ? 'module' : 'block';

        $this->postStatus = $post->post_status ?? 'publish';

        $this->init();

        // Defaults to the path of the class .php-file and subdir /views
        // Example: my-module/my-module.php (module class)
        //          my-module/views/        (views folder)
        if(!$this->templateDir || !$this->assetDir) {
            $reflector = new \ReflectionClass(get_class($this));

            if (!$this->templateDir) {
                $this->templateDir = trailingslashit(dirname($reflector->getFileName())) . 'views/';
            }
    
            if (!$this->assetDir) {
                $this->assetDir = trailingslashit(dirname($reflector->getFileName())) . 'assets/';
            }
        }

        if (is_a($post, '\WP_Post')) {
            $this->extractPostProperties($post);
            $this->collectViewData();
        }

        WpService::get()->addAction('admin_enqueue_scripts', array($this, 'adminEnqueue'));


        $this->data['postTitle'] = $post->post_title ?? false;

        if (!is_admin()) {
            WpService::get()->addAction('wp_enqueue_scripts', function () {

                if ($this->hasModule()) {
                    if (method_exists($this, 'style')) {
                        $this->style();
                    }

                    if (method_exists($this, 'script')) {
                        $this->script();
                    }
                }
            });
        }

        WpService::get()->addAction('save_post', function($postID, $post, $update) {
            WpService::get()->wpCacheDelete('modularity_has_modules_' . $postID);
        }, 1, 3);
    }

    public function init()
    {
    }

    /**
     * Method to enqueu styles when module exists on page
     * @return void
     */
    public function style()
    {
        // Put styles here
    }

    /**
     * Method to enqueu scripts when module exists on page
     * @return void
     */
    public function script()
    {
        // Put scripts here
    }

    /**
     * Enqueue for admin
     * @return void
     */
    public function adminEnqueue()
    {
        if (\Modularity\Helper\Wp::isAddOrEditOfPostType($this->moduleSlug)) {
            WpService::get()->doAction('Modularity/Module/' . $this->moduleSlug . '/enqueue');
        }
    }

    /**
     * Extracts WP_Post properties into Module properties
     * @param  \WP_Post $post
     * @return void
     */
    private function extractPostProperties(\WP_Post $post): void
    {
        foreach ($post as $key => $value) {
            $this->extractedPostProperties[] = $key;
            $this->data[$key] = $value;

            // Fix for PHP8, avoid creation of dynamic property.
            // https://www.php.net/manual/en/migration80.incompatible.php#migration80.incompatible.variable-handling.indirect
            // Variables that needs to be avabile, must be defined in class.
            if(property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }

    public function data(): array
    {
        return array();
    }

    /**
     * Check if the module is in inline mode (eg. in shortcode etc)
     * @return boolean
     */
    public function isInline(): bool
    {
        return (bool) ($this->args['inline'] ?? false);
    }

    /**
     * Get module view
     * @return string
     */
    public function template()
    {
        if (!$this->isLegacy && !empty($this->slug)) {
            return $this->slug . '.blade.php';
        }

        return false;
    }

    /**
     * Get module view data
     * @return void
     */
    public function collectViewData()
    {
        $this->data = array_merge($this->data, $this->data());
    }

    /**
     * Get the ID of the module or block
     * @return int|string|null
     */
    protected function getID(): int|string|null
    {
        if ($this->ID) {
            $id = $this->ID;
        } else {
            $id = acf_get_valid_post_id( false );
        }

        return $this->ID = $id ?: null;
    }

    /**
     * Get metadata for block or module.
     * @return array
     */
    protected function getFields(): array
    {
        $this->dataFetched = true;
        return AcfService::get()->getFields($this->mode === 'module' ? $this->getID() : []) ?: [];
    }

    private function getBlockNamesFromPage(): array
    {
        static $blocks;

        if (is_array($blocks)) {
            return $blocks;
        }

        $post = WpService::get()->getPost(\Municipio\Helper\CurrentPostId::get());

        if (empty($post->post_content)) {
            return $blocks = [];
        }

        $blockNamesRegex = '/<!--\s*wp:acf\/([a-zA-Z0-9_-]+)/';
        preg_match_all($blockNamesRegex, $post->post_content, $matches);

        $blocks = $matches[1] ?? [];

        $blocks = array_map(function ($block) {
            return 'mod-' . $block;
        }, $blocks);

        return $blocks;
    }

    /**
     * Checks if a current page/post has module(s) of this type
     * @return boolean
     */
    protected function hasModule()
    {
        global $post;

        $postId = null;
        $modules = array();
        $archiveSlug = \Modularity\Helper\Wp::getArchiveSlug();

        if ($archiveSlug) {
            $postId = $archiveSlug;
        } elseif (isset($post->ID)) {
            $postId = $post->ID;
        } else {
            return WpService::get()->applyFilters('Modularity/hasModule', true, null);
        }

        //Get modules
        $modules = $this->getPresentModuleList($postId);
        
        //Look for
        $moduleSlug = $this->moduleSlug;
        if (empty($moduleSlug)) {
            $moduleSlug = isset($this->data['post_type']) ? $this->data['post_type'] : null;
        }

        return WpService::get()->applyFilters(
            'Modularity/hasModule',
            in_array($moduleSlug, $modules),
            $archiveSlug
        );
    }

    /**
     * Get a list of present modules in this context   
     * @param  integer $postId
     * @return array
     */
    private function getPresentModuleList($postId): array
    {

        //Return cached modules
        if($cachedModules = WpService::get()->wpCacheGet('modularity_has_modules_' . $postId)) {
            return $cachedModules;
        }

        $modules = []; 

        //Get each module link type
        $modulesByLinkType = [
            'meta'          => $this->getValueFromKeyRecursive(
                                    \Modularity\Editor::getPostModules($postId), 
                                    'post_type'
            ),
            'shortcodes'    => $this->getShortcodeModules($postId),
            'blocks'        => $this->getBlockNamesFromPage(),
            'widgets'       => $this->getWidgets(),
        ];  

        //Filter and merge all modules
        foreach($modulesByLinkType as $modulesLinkType) {
            $modules = array_merge(
                $modules, 
                $modulesLinkType
            );
        }

        //Remove duplicates
        $modules = array_unique($modules);

        //Set cache
        WpService::get()->wpCacheSet('modularity_has_modules_' . $postId, $modules);

        return $modules;
    }

    /**
     * Get values from array recursively
     *
     * @param array $haystack
     * @param string $needle
     * @return array
     */
    private function getValueFromKeyRecursive(array $haystack, $needle): array
    {
        $stack = [];
        $iterator  = new \RecursiveArrayIterator($haystack);
        $recursive = new \RecursiveIteratorIterator(
            $iterator,
            \RecursiveIteratorIterator::SELF_FIRST
        );
        foreach ($recursive as $key => $value) {
            if ($key === $needle) {
                $stack[] = $value;
            }
        }

        return array_unique(array_filter($stack));
    }

    /**
     * Retrieve and process widgets to extract module names.
     *
     * @return array An array containing module names extracted from widgets.
     */
    private function getWidgets(): array{
        $widgets = WpService::get()->getOption('widget_block');

        $modules = [];
        if (!empty($widgets) && is_array($widgets)) {
            foreach ($widgets as $widget) {
                $moduleNames = $this->getWidgetNames($widget); 
                
                if (!empty($moduleNames) && is_array($moduleNames)) {
                    foreach ($moduleNames as $moduleName) {
                        $modules[] = $moduleName;
                    }
                }
            }
        }

        return $modules;
    }

    /**
     * Extract and return the module name from a given widget.
     *
     * @param array $widget The widget data array.
     * @return array The extracted module name or false if not found.
     */
    private function getWidgetNames($widget): array|false {

        if (!is_array($widget) || empty($widget['content'])) {
            return false;
        }
        
        $modules = [];
        
        preg_match_all('/<!--\s*wp:acf\/(\S+).*?\s*-->/s', $widget['content'], $matches);

        if (!empty($matches[1]) && is_array($matches[1])) {
            foreach ($matches[1] as $match) {
                if (!empty($match)) {
                    $modules[$match] = 'mod-' . $match;
                }
            }
        }

        preg_match_all('/id="(\d+)"/', $widget['content'], $shortCodeIds);

        if (!empty($shortCodeIds[1]) && is_array($shortCodeIds[1])) {
            foreach ($shortCodeIds[1] as $shortCodeId) {
                $module = WpService::get()->getPostType(intval($shortCodeId));

                if (!empty($module)) {
                    $modules[$module] = $module;
                }
            }
        }
        
        return $modules;
    }

    /**
     * Get modules used in shortcodes
     * @param  string $post_id Current post id
     * @return array           Array with module post types
     */
    public function getShortcodeModules($post_id): array
    {
        if(is_numeric($post_id) === false || $post_id <= 0) {
            return [];
        }

        $post_id = intval($post_id);
        $post = WpService::get()->getPost($post_id);
        $pattern = WpService::get()->getShortcodeRegex();
        $modules = array();

        if (
            is_object($post) && preg_match_all('/' . $pattern . '/s', $post->post_content, $matches)
            && array_key_exists(2, $matches)
            && in_array('modularity', $matches[2])
        ) {
            $shortcodes = preg_replace('/[^0-9]/', '', $matches[3]);
            foreach ($shortcodes as $key => $shortcode) {
                $modules[] = get_post_type($shortcode);
            }
        }

        return $modules;
    }
}
