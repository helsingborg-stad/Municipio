<?php

namespace Modularity;

use enshrined\svgSanitize\Sanitizer as SVGSanitize;


class ModuleManager
{
    /**
     * Prefix for module slugs
     * @var  string
     */
    const MODULE_PREFIX = 'mod-';

    /**
     * Holds all module's settings
     * @var array
     */
    public static $moduleSettings = array();

    /**
     * Holds a list of registered modules
     * @var array
     */
    public static $registered = array();

    /**
     * Holds namespaces for each and every module class
     * @var array
     */
    public static $classes = array();

    /**
     * Holds a list of available (initialized) modules
     * @var array
     */
    public static $available = array();

    /**
     * Holds a list of enabled modules
     * @var array
     */
    public static $enabled = array();

    /**
     * Holds a list of deprecated modules
     * @var array
     */
    public static $deprecated = array();

    /**
     * Available width settings for modules
     * @var array
     */
    public static $widths = array();

    public static $blockManager = null;

    public function __construct()
    {
        self::$blockManager = new \Modularity\BlockManager();

        // Init modules
        add_action('init', function () {
            self::$enabled    = self::getEnabled();
            self::$registered = $this->getRegistered();

            $this->init();
        }, 10);

        // Hide title option
        add_action('edit_form_before_permalink', array($this, 'hideTitleCheckbox'));
        add_action('save_post', array($this, 'saveHideTitleCheckbox'), 10, 2);

        // Shortcode metabox
        add_action('add_meta_boxes', array($this, 'shortcodeMetabox'));

        // Add usage metabox
        add_action('add_meta_boxes', array($this, 'whereUsedMetaBox'));

        // Description meta box
        add_action('add_meta_boxes', array($this, 'descriptionMetabox'), 5);
        add_action('save_post', array($this, 'descriptionMetaboxSave'));

        // Possibly show notice nag when editing module used in several places
        add_action('admin_notices', [$this, 'maybeShowEditModuleUsageNoticeNag']);
    }

    /**
     * Get available modules (WP filter)
     * @return array
     */
    public function getRegistered($getBundled = true)
    {
        if ($getBundled) {
            $bundeled   = $this->getBundeled();
            self::$registered = array_merge(self::$registered, $bundeled);
        }

        return apply_filters('Modularity/Modules', self::$registered);
    }

    /**
     * Get enabled modules id: s
     * @return array
     */
    public static function getEnabled()
    {
        $options = get_option('modularity-options');

        if (!isset($options['enabled-modules'])) {
            return array();
        }

        return $options['enabled-modules'];
    }

    /**
     * Gets bundeled modules
     * @return array
     */
    public function getBundeled(): array
    {
        $directory = MODULARITY_PATH . 'source/php/Module/';
        $bundeled  = array();

        foreach (@glob($directory . "*", GLOB_ONLYDIR) as $folder) {
            $bundeled[$folder] = basename($folder);
        }

        return $bundeled;
    }

    /**
     * Initializes modules
     * @return void
     */
    public function init()
    {
        foreach (self::$registered as $path => $module) {
            $path      = trailingslashit($path);
            $source    = $path . $module . '.php';
            $namespace = \Modularity\Helper\File::getModuleNamespace($source);

            if (!$namespace) {
                continue;
            }

            require_once $source;
            $class = $namespace . '\\' . $module;
            $class = new $class();

            $this->register($class, $path);
            self::$blockManager->classes[$class->slug] = $class;
        }

        self::$blockManager->registerBlocks();

        do_action('Modularity/Init', $this);
    }

    /**
     * Registers a module with all it's components (post types etc)
     * @param  object $class Module class (\Modularity\Module extension)
     * @param  string $path  Path to module
     * @return string        Module's post type slug
     */
    public function register($class, string $path = '')
    {
        if (empty($class->slug)) {
            return;
        }

        if (!is_multisite() && $class->multisiteOnly) {
            return;
        }

        $postTypeSlug           = self::prefixSlug($class->slug);
        self::$classes[$postTypeSlug] = $class;

        // Set labels
        $labels = array(
            'name'               => _x($class->nameSingular, 'post type general name', 'modularity'),
            'singular_name'      => _x($class->nameSingular, 'post type singular name', 'modularity'),
            'menu_name'          => _x($class->namePlural, 'admin menu', 'modularity'),
            'name_admin_bar'     => _x($class->nameSingular, 'add new on admin bar', 'modularity'),
            'add_new'            => _x('Add New', 'add new button', 'modularity'),
            'add_new_item'       => sprintf(__('Add new %s', 'modularity'), $class->nameSingular),
            'new_item'           => sprintf(__('New %s', 'modularity'), $class->nameSingular),
            'edit_item'          => sprintf(__('Edit %s', 'modularity'), $class->nameSingular),
            'view_item'          => sprintf(__('View %s', 'modularity'), $class->nameSingular),
            'all_items'          => sprintf(__('Edit %s', 'modularity'), $class->namePlural),
            'search_items'       => sprintf(__('Search %s', 'modularity'), $class->namePlural),
            'parent_item_colon'  => sprintf(__('Parent %s', 'modularity'), $class->namePlural),
            'not_found'          => sprintf(__('No %s', 'modularity'), $class->namePlural),
            'not_found_in_trash' => sprintf(__('No %s in trash', 'modularity'), $class->namePlural)
        );

        // Set args
        $args = array(
            'labels'              => $labels,
            'description'         => __($class->description, 'modularity'),
            'public'              => false,
            'publicly_queryable'  => false,
            'exclude_from_search' => false,
            'show_ui'             => true,
            'show_in_nav_menus'   => false,
            'show_in_menu'        => ($this->showInAdminMenu()) ? 'modularity' : false,
            'has_archive'         => false,
            'rewrite'             => false,
            'hierarchical'        => false,
            'menu_position'       => 100,
            'menu_icon'           => $class->icon,
            'supports'            => array_merge($class->supports, array('title', 'revisions', 'author')),
            'capabilities'        => array(
                'edit_post'          => 'edit_module',
                'edit_posts'         => 'edit_modules',
                'edit_others_posts'  => 'edit_other_modules',
                'publish_posts'      => 'publish_modules',
                'read_post'          => 'read_module',
                'read_private_posts' => 'read_private_posts',
                'delete_post'        => 'delete_module'
            ),
            'map_meta_cap' => true
        );

        //Disable from search search pages (someone did a huge mistake designing this feature)
        if (is_search()) {
            $args['exclude_from_search'] = true;
        }

        // Get menu icon
        if (empty($args['menu_icon']) && $icon = self::getIcon($class)) {
            $args['menu_icon']             = $icon;
            $args['menu_icon_auto_import'] = true;
        }

        // Register the post type if module is enabled
        if (in_array($postTypeSlug, self::$enabled)) {
            register_post_type($postTypeSlug, $args);
            $this->setupListTableField($postTypeSlug);

            $class->plugin = (array) $class->plugin;

            if (!empty($class->plugin)) {
                // Fallback to enable old modules to register their plugins
                if (!is_array($class->plugin)) {
                    $class->plugin = array($class->plugin);
                }

                // Ability to filter path
                $class->plugin = apply_filters('Modularity/Register/Plugin', $class->plugin, $postTypeSlug);

                // Include
                foreach ($class->plugin as $plugin) {
                    /**
                     * Deprecated
                     * @todo  Remove……
                     */
                    trigger_error('Deprecation message: Modularity module "' . $postTypeSlug . '" is using a deprecated way of including plugins. Use the wp action "Modularity/Plugins" to load plugins instead.', E_USER_WARNING);

                    if (file_exists($plugin)) {
                        require_once $plugin;
                    } elseif (file_exists(MODULARITY_PATH . 'plugins/' . $plugin)) {
                        require_once MODULARITY_PATH . 'plugins/' . $plugin;
                    }
                }
            }
        }

        // Check if module is deprecated
        if ($class->isDeprecated) {
            \Modularity\ModuleManager::$deprecated[] = $postTypeSlug;
        }

        // Add to list of available modules
        \Modularity\ModuleManager::$available[$postTypeSlug] = $args;

        // Store settings of each module in static var
        self::$moduleSettings[$postTypeSlug] = array(
            'moduleSlug'    => $postTypeSlug,
            'slug'          => $class->slug,
            'singular_name' => $class->nameSingular,
            'plural_name'   => $class->namePlural,
            'description'   => $class->description,
            'supports'      => $class->supports,
            'icon'          => $class->icon,
            'plugin'        => $class->plugin,
            'cache_ttl'     => $class->cacheTtl,
            'hide_title'    => $class->hideTitle
        );

        $class->moduleSlug = $postTypeSlug;

        return $postTypeSlug;
    }

    /**
     * Prefixes module post type slug if needed
     * @param  string $slug
     * @return string
     */
    public static function prefixSlug(string $slug): string
    {
        if (substr($slug, 0, strlen(self::MODULE_PREFIX)) !== self::MODULE_PREFIX) {
            $slug = self::MODULE_PREFIX . $slug;
        }

        if (strlen($slug) > 20) {
            $slug = substr($slug, 0, 20);
        }

        $slug = strtolower($slug);

        return $slug;
    }

    /**
     * Get bundeled icon
     * @param  string $path Path to module folder
     * @return string
     */
    public static function getIcon($class): string
    {
        //Look for icon (including cleaning)
        if ($class->assetDir && file_exists($class->assetDir . 'icon.svg')) {
            $sanitizer = new SVGSanitize();
            $sanitizer->minify(true);
            $sanitizer->removeXMLTag(true);
            return $sanitizer->sanitize(
                file_get_contents($class->assetDir . 'icon.svg')
            );
        }
        return '';
    }

    /**
     * Check if the module should be displayed in the admin menu
     * @return boolean
     */
    public function showInAdminMenu()
    {
        $options = get_option('modularity-options');

        if (isset($options['show-modules-in-menu']) && $options['show-modules-in-menu'] == 'on') {
            return true;
        }

        return false;
    }

    /**
     * Adds checkbox to post edit page to hide title
     * @return void
     */
    public function hideTitleCheckbox()
    {
        global $post;

        if (substr($post->post_type, 0, 4) != 'mod-') {
            return;
        }

        $current = self::$moduleSettings[$post->post_type]['hide_title'];

        if (strlen(get_post_meta($post->ID, 'modularity-module-hide-title', true)) > 0) {
            $current = boolval(get_post_meta($post->ID, 'modularity-module-hide-title', true));
        }

        $checked = checked(true, $current, false);

        echo '<div>
            <label style="cursor:pointer;">
            <input type="checkbox" name="modularity-module-hide-title" value="1" ' . $checked . '>
                ' . __('Hide title', 'modularity') . '
            </label>
        </div>';
    }

    /**
     * Saves the hide title checkboc
     * @param  int $postId
     * @param  WP_Post $post
     * @return void
     */
    public function saveHideTitleCheckbox($postId, $post)
    {
        if (substr($post->post_type, 0, 4) != 'mod-') {
            return;
        }

        if (!isset($_POST['modularity-module-hide-title'])) {
            update_post_meta($post->ID, 'modularity-module-hide-title', 0);
            return;
        }

        update_post_meta($post->ID, 'modularity-module-hide-title', 1);
        return;
    }

    /**
     * Shortcode metabox
     * @return void
     */
    public function shortcodeMetabox()
    {
        if (empty(self::$enabled)) {
            return;
        }

        add_meta_box('modularity-shortcode', 'Modularity Shortcode', function () {
            global $post;
            echo '<p>';
            echo __('Copy and paste this shortcode to display the module inline.', 'modularity');
            echo '</p><p>';
            echo '<textarea style="margin-top:10px; overflow: hidden;width: 100%;height:30px;background:#f9f9f9;border:1px solid #ddd;padding:5px;">[modularity id="' . $post->ID . '"]</textarea>';
            echo '</p>';
        }, self::$enabled, 'side', 'default');
    }

    /**
     * Metabox that shows where the module is used
     * @return void
     */
    public function whereUsedMetaBox()
    {
        if (empty(self::$enabled)) {
            return;
        }

        global $post;
        $module = $this;

        if (is_null($post)) {
            return;
        }

        $usage = self::getModuleUsage($post->ID);

        add_meta_box('modularity-usage', 'Module usage', function () use ($module, $usage) {
            if (count($usage) == 0) {
                echo '<p>' . __('This modules is not used yet.', 'modularity')  . '</p>';
                return;
            }

            echo '<p>' . __('This module is used on the following places:', 'modularity') . '</p><p><ul class="modularity-usage-list">';

            foreach ($usage as $page) {
                echo '<li><a href="' . get_permalink($page->post_id) . '">' . $page->post_title . '</a></li>';
            }

            echo '</ul></p>';
        }, self::$enabled, 'side', 'default');
    }

    /**
     * Search database for where the module is used
     * @param  integer $id Module id
     * @return array       List of pages where the module is used
     */
    public static function getModuleUsage($id, $limit = false)
    {
        return \Modularity\Helper\ModuleUsageById::getModuleUsageById($id, $limit);
    }

    /**
     * Search database for what pages have specific modules.
     * @param  integer $postType Post type of the module
     * @return array       List of pages where the module is used
     */
    public static function getModulesUsageByName($postType)
    {
        return \Modularity\Helper\ModuleUsageByName::getModuleUsageByName($postType);
    }

    /**
     * Description metabox content
     * @return void
     */
    public function descriptionMetabox()
    {
        if (empty(self::$enabled)) {
            return;
        }

        add_meta_box(
            'modularity-description',
            __('Module description', 'modularity'),
            function () {
                $description = get_post_meta(get_the_id(), 'module-description', true);
                include MODULARITY_TEMPLATE_PATH . 'editor/modularity-module-description.php';
            },
            self::$enabled,
            'normal',
            'high'
        );
    }

    /**
     * Saves the description
     * @return void
     */
    public function descriptionMetaboxSave()
    {
        if (!isset($_POST['modularity-module-description'])) {
            return;
        }

        update_post_meta(intval($_POST['post_ID']), 'module-description', trim($_POST['modularity-module-description']));
    }

    /**
     * Setup list table fields
     * @return void
     */
    public function setupListTableField($slug)
    {
        add_filter('manage_edit-' . $slug . '_columns', array($this, 'listTableColumns'));
        add_action('manage_' . $slug . '_posts_custom_column', array($this, 'listTableColumnContent'), 10, 2);
        add_filter('manage_edit-' . $slug . '_sortable_columns', array($this, 'listTableColumnSorting'));
    }

    /**
     * Define list table columns
     * @param  array $columns  Default columns
     * @return array           Modified columns
     */
    public function listTableColumns($columns)
    {
        $columns = array(
            'cb'          => '<input type="checkbox">',
            'title'       => __('Title'),
            'description' => __('Description'),
            'usage'       => __('Usage', 'modularity'),
            'date'        => __('Date')
        );

        return $columns;
    }

    /**
     * List table column content
     * @param  string $column  Column
     * @param  integer $postId Post id
     * @return void
     */
    public function listTableColumnContent($column, $postId)
    {
        switch ($column) {
            case 'description':
                $description = get_post_meta($postId, 'module-description', true);
                echo !empty($description) ? $description : '';
                break;

            case 'usage':
                $usage = self::getModuleUsage($postId, 3);

                if (count($usage->data) == 0) {
                    echo __('Not used', 'modularity');
                    break;
                }

                $i = 0;

                foreach ($usage->data as $item) {
                    $i++;

                    if ($i > 1) {
                        echo ', ';
                    }

                    echo '<a href="' . get_permalink($item->post_id) . '">' . $item->post_title . '</a>';
                }

                if ($usage->more > 0) {
                    echo ' (' . $usage->more . ' ' . __('more', 'modularity') . ')';
                }

                break;
        }
    }

    /**
     * Table list column sorting
     * @param  array $columns Default sortable columns
     * @return array          Modified sortable columns
     */
    public function listTableColumnSorting($columns)
    {
        $columns['description'] = 'description';
        $columns['usage']       = 'usage';
        return $columns;
    }

    /**
     * Possibly adds a notice nag if a module is used in more than one place
     * @return void
     */
    public function maybeShowEditModuleUsageNoticeNag() {
        $options = get_option('modularity-options');

        // Add admin notice about module usage if setting is enabled
        if (isset($options['show-modules-usage-edit-notice-nag']) && $options['show-modules-usage-edit-notice-nag'] == 'on') {
            $screen = get_current_screen();
            $usage = sizeof(ModuleManager::getModuleUsage(get_the_ID()));

            if (strpos($screen->post_type, 'mod-') === 0 && $usage > 1) {
                echo '<div class="notice notice-warning">';
                echo '<p>' . __('<strong>Heads up:</strong> This module is used in several places', 'modularity') . '</p>';
                echo '</div>';
            }
        }
    }
}
