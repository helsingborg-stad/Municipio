<?php

namespace Modularity;

use Modularity\Helper\WpService;
use Modularity\Private\PrivateAcfFields;

class App
{
    public static $display = null;
    public static $moduleManager = null;
    public $editor = null;


    public function __construct()
    {
        add_action('admin_enqueue_scripts', array($this, 'enqueueAdmin'), 950);
        add_action('enqueue_block_editor_assets', array($this, 'enqueueBlockEditor'));
        add_action('wp_enqueue_scripts', array($this, 'enqueueFront'), 950);
        add_action('admin_menu', array($this, 'addAdminMenuPage'));
        add_action('admin_init', array($this, 'addCaps'));

        add_filter('acf/fields/post_object/query', array($this, 'removeFromAcfPostQuery'), 99, 3);

        // Main hook
        do_action('Modularity');

        /**
         * Redirect top level Modularity page to the Modularity options page
         */
        add_action('load-toplevel_page_modularity', function () {
            wp_redirect(admin_url('admin.php?page=modularity-options'));
        });

        $this->setupAdminBar();

        new Ajax();
        new Options\General();

        $upgradeInstance = new Upgrade();
        new WpCli($upgradeInstance);

        $archivesAdminPage = new Options\ArchivesAdminPage();
        $archivesAdminPage->addHooks();
        $optionsForSingleViews = new Options\SingleAdminPage();
        $optionsForSingleViews->addHooks();

        // Rest Controllers
        $modulesRestController = new Api\V1\Modules();
        $modulesRestController->register_routes();

        self::$moduleManager = new ModuleManager();


        $this->editor = new Editor();
        self::$display = new Display();

        if (is_admin()) {
            new PrivateAcfFields();
        }

        new Helper\Acf();
        new CachePurge();

        new Search();

        new Language();

        add_action('post_updated', [$this, 'updatePostModifiedDateOnPostsRelatedToModule'], 10, 2);
        add_action('updated_post_meta', [$this, 'updatePostModifiedDateOnMetaUpdate'], 10, 4);
        add_action('deleted_post_meta', [$this, 'updatePostModifiedDateOnMetaUpdate'], 10, 4);

        add_action('widgets_init', function () {
            register_widget('\Modularity\Widget');
        });
    }


    public function addCaps()
    {
        $admin = get_role('administrator');
        if (is_a($admin, 'WP_Role') && $admin->has_cap('edit_module')) {
            return;
        }

        $caps = array(
            'administrator' => array(
                'edit_module',
                'edit_modules',
                'edit_other_modules',
                'publish_modules',
                'read_modules',
                'delete_module'
            ),
            'editor' => array(
                'edit_module',
                'edit_modules',
                'edit_other_modules',
                'publish_modules',
                'read_modules',
                'delete_module'
            ),
            'author' => array(
                'edit_module',
                'edit_modules',
                'edit_other_modules',
                'publish_modules',
                'read_modules'
            )
        );

        foreach ($caps as $roleId => $cap) {
            $role = get_role($roleId);

            foreach ($cap as $item) {
                $role->add_cap($item);
            }
        }
    }

    /**
     * Add buttons to admin bar (public)
     * @return void
     */
    public function setupAdminBar()
    {
        // Link to editor from page
        add_action('admin_bar_menu', function () {

            if (is_admin() || !current_user_can('edit_posts')) {
                return;
            }

            $options = get_option('modularity-options');

            global $wp_admin_bar;
            global $post;

            $editorLink = admin_url('options.php?page=modularity-editor&id=' . get_the_id());

            $archiveSlug = \Modularity\Helper\Wp::getArchiveSlug();
            if ($archiveSlug && $postId = \Modularity\Editor::pageForPostTypeTranscribe($archiveSlug)) {
                $editorLink = admin_url('options.php?page=modularity-editor&id=' . $postId);
            }

            if (isset($options['enabled-post-types']) && is_array($options['enabled-post-types']) && !in_array(get_post_type(), $options['enabled-post-types'])) {
                $editorLink = null;
            }

            $editorLink = apply_filters(
                'Modularity/adminbar/editor_link',
                $editorLink,
                $post,
                $archiveSlug,
                $this->currentUrl()
            );

            if (empty($editorLink)) {
                return;
            }

            $wp_admin_bar->add_node(array(
                'id' => 'modularity_editor',
                'title' => __('Edit', 'modularity') . ' ' . strtolower(__('Modules', 'modularity')),
                'href' => $editorLink,
                'meta' => array(
                    'class' => 'modularity-editor-icon'
                )
            ));
        }, 1050);
    }

    public function currentUrl($querystring = true)
    {
        $url =  '//' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

        if (!$querystring) {
            $url = preg_replace('/\?(.*)/', '', $url);
        }

        return $url;
    }

    public function enqueueFront()
    {
        wp_register_style('modularity', MODULARITY_URL . '/dist/'
            . \Modularity\Helper\CacheBust::name('css/modularity.css'));
        wp_enqueue_style('modularity');

        wp_register_script('modularity', MODULARITY_URL . '/dist/'
            . \Modularity\Helper\CacheBust::name('js/modularity.js'), [], null, true);

        wp_localize_script('modularity', 'modularityAdminLanguage', array(
            'langvisibility' => __('Toggle visibility', 'modularity'),
            'langedit' => __('Edit', 'modularity'), 
            'langimport' => __('Import', 'modularity'),
            'langremove' => __('Remove', 'modularity'),
            'langhide' => __('Hide module', 'modularity'),
            'actionRemove' => __('Are you sure you want to remove this module?', 'modularity'),
            'isSaving' => __('Saving…', 'modularity'),
            'close' => __('Close', 'modularity'),
            'width' => __('Width', 'modularity'),
            'widthOptions' => $this->editor->getWidthOptions(),
            'deprecated' => __('Deprecated', 'modularity')
        ));
        wp_enqueue_script('modularity');

        wp_register_script('user-editable-list', MODULARITY_URL . '/dist/'
        . \Modularity\Helper\CacheBust::name('js/user-editable-list.js'));

        wp_enqueue_script('user-editable-list');

        if (!current_user_can('edit_posts')) {
            return;
        }
        //Register admin specific scripts/styling here

        if (wp_script_is('jquery', 'registered') && !wp_script_is('jquery', 'enqueued')) {
            wp_enqueue_script('jquery');
        }
    }

    public function enqueueBlockEditor() {

        if ($modulesEditorId = \Modularity\Helper\Wp::isGutenbergEditor()) {
            wp_register_script(
                'block-editor-edit-modules',
                MODULARITY_URL . '/dist/' . \Modularity\Helper\CacheBust::name('js/edit-modules-block-editor.js'),
                array('wp-plugins', 'wp-edit-post', 'wp-element', 'wp-components'), // dependencies
                null,
                true 
            );

            wp_register_script('block-editor-validation', MODULARITY_URL . '/dist/'
            . \Modularity\Helper\CacheBust::name('js/block-validation.js'), [], null, ['in_footer' => true]);

            wp_localize_script('block-editor-edit-modules', 'modularityBlockEditor', array(
                'editModulesLinkLabel' => __('Edit Modules', 'modularity'),
                'editModulesLinkHref' => admin_url('options.php?page=modularity-editor&id=' . $modulesEditorId)
            ));

            wp_enqueue_script('block-editor-validation');
            wp_enqueue_script('block-editor-edit-modules');
        }
    }

    /**
     * Enqueues scripts and styles
     * @return void
     */
    public function enqueueAdmin()
    {
        if (!$this->isModularityPage()) {
            return;
        }

        wp_register_style('modularity', MODULARITY_URL . '/dist/'
        . \Modularity\Helper\CacheBust::name('css/modularity.css'));
        wp_enqueue_style('modularity');

        wp_register_script('modularity', MODULARITY_URL . '/dist/'
        . \Modularity\Helper\CacheBust::name('js/modularity.js'), ['wp-api'], null, true);
        wp_localize_script('modularity', 'modularityAdminLanguage', array(
            'langvisibility' => __('Toggle visibility', 'modularity'),
            'langedit' => __('Edit', 'modularity'),
            'langimport' => __('Import', 'modularity'),
            'langremove' => __('Remove', 'modularity'),
            'langhide' => __('Hide module', 'modularity'),
            'actionRemove' => __('Are you sure you want to remove this module?', 'modularity'),
            'isSaving' => __('Saving…', 'modularity'),
            'close' => __('Close', 'modularity'),
            'width' => __('Width', 'modularity'),
            'widthOptions' => $this->editor->getWidthOptions(),
            'deprecated' => __('Deprecated', 'modularity')
        ));
        wp_enqueue_script('modularity');

        wp_register_script('dynamic-map-acf', MODULARITY_URL . '/dist/'
        . \Modularity\Helper\CacheBust::name('js/dynamic-map-acf.js'), ['jquery']);
        wp_enqueue_script('dynamic-map-acf');

        wp_register_script('modularity-text-module', MODULARITY_URL . '/dist/'
        . \Modularity\Helper\CacheBust::name('js/modularity-text-module.js'));
        wp_enqueue_script('modularity-text-module');

        add_action('admin_head', function () {
            echo "
                <script>
                    var admin_url = '" . admin_url() . "';
                </script>
            ";
        });

        add_action('admin_head', function () {
            echo "
                <script>
                    if(typeof $ === 'undefined' && typeof jQuery !== 'undefined') {
                        var $ = jQuery;
                    }
                </script>
            ";
        });

        // If editor
        if (\Modularity\Helper\Wp::isEditor()) {
            wp_enqueue_script('jquery-ui-sortable');
            wp_enqueue_script('jquery-ui-draggable');
            wp_enqueue_script('jquery-ui-droppable');

            add_action('admin_head', function () {
                global $post;
                global $archive;

                if (isset($_GET['id']) && is_numeric($_GET['id']) && get_post_status($_GET['id'])) {
                    $id = $_GET['id'];
                } else {
                    $id = isset($post->ID) ? $post->ID : "'" . $archive . "'";
                }

                echo "
                    <script>
                        var modularity_post_id = " . $id . "
                    </script>
                ";
            }, 10);
        }
    }

    /**
     * Check if current page is a modularity page
     * @return boolean
     */
    public function isModularityPage()
    {
        $currentScreen = get_current_screen();

        if (!($currentScreen instanceof \WP_Screen)) {
            return false;
        }

        $id = $currentScreen->id;
        $action = $currentScreen->action;
        $base = $currentScreen->base;

        $isModularityPage = strpos($id, 'modularity') !== false || strpos($id, 'mod-') !== false;
        $isModularityPage |= isset($_GET['action']) && $_GET['action'] === 'edit' && $action === 'add';
        $isModularityPage |= in_array($base, ['post', 'widgets']);

        return $isModularityPage;
    }

    public function addAdminMenuPage()
    {
        add_menu_page(
            'Modularity',
            'Modularity',
            'manage_options',
            'modularity',
            function () {

            },
            'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pg0KPCEtLSBHZW5lcmF0b3I6IEFkb2JlIElsbHVzdHJhdG9yIDE2LjAuMCwgU1ZHIEV4cG9ydCBQbHVnLUluIC4gU1ZHIFZlcnNpb246IDYuMDAgQnVpbGQgMCkgIC0tPg0KPCFET0NUWVBFIHN2ZyBQVUJMSUMgIi0vL1czQy8vRFREIFNWRyAxLjEvL0VOIiAiaHR0cDovL3d3dy53My5vcmcvR3JhcGhpY3MvU1ZHLzEuMS9EVEQvc3ZnMTEuZHRkIj4NCjxzdmcgdmVyc2lvbj0iMS4xIiBpZD0iQ2FwYV8xIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHhtbG5zOnhsaW5rPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5L3hsaW5rIiB4PSIwcHgiIHk9IjBweCINCgkgd2lkdGg9IjU0Ljg0OXB4IiBoZWlnaHQ9IjU0Ljg0OXB4IiB2aWV3Qm94PSIwIDAgNTQuODQ5IDU0Ljg0OSIgc3R5bGU9ImVuYWJsZS1iYWNrZ3JvdW5kOm5ldyAwIDAgNTQuODQ5IDU0Ljg0OTsiDQoJIHhtbDpzcGFjZT0icHJlc2VydmUiPg0KPGc+DQoJPGc+DQoJCTxnPg0KCQkJPHBhdGggZD0iTTU0LjQ5NywzOS42MTRsLTEwLjM2My00LjQ5bC0xNC45MTcsNS45NjhjLTAuNTM3LDAuMjE0LTEuMTY1LDAuMzE5LTEuNzkzLDAuMzE5Yy0wLjYyNywwLTEuMjU0LTAuMTA0LTEuNzktMC4zMTgNCgkJCQlsLTE0LjkyMS01Ljk2OEwwLjM1MSwzOS42MTRjLTAuNDcyLDAuMjAzLTAuNDY3LDAuNTI0LDAuMDEsMC43MTZMMjYuNTYsNTAuODFjMC40NzcsMC4xOTEsMS4yNTEsMC4xOTEsMS43MjksMEw1NC40ODgsNDAuMzMNCgkJCQlDNTQuOTY0LDQwLjEzOSw1NC45NjksMzkuODE3LDU0LjQ5NywzOS42MTR6Ii8+DQoJCQk8cGF0aCBkPSJNNTQuNDk3LDI3LjUxMmwtMTAuMzY0LTQuNDkxbC0xNC45MTYsNS45NjZjLTAuNTM2LDAuMjE1LTEuMTY1LDAuMzIxLTEuNzkyLDAuMzIxYy0wLjYyOCwwLTEuMjU2LTAuMTA2LTEuNzkzLTAuMzIxDQoJCQkJbC0xNC45MTgtNS45NjZMMC4zNTEsMjcuNTEyYy0wLjQ3MiwwLjIwMy0wLjQ2NywwLjUyMywwLjAxLDAuNzE2TDI2LjU2LDM4LjcwNmMwLjQ3NywwLjE5LDEuMjUxLDAuMTksMS43MjksMGwyNi4xOTktMTAuNDc5DQoJCQkJQzU0Ljk2NCwyOC4wMzYsNTQuOTY5LDI3LjcxNiw1NC40OTcsMjcuNTEyeiIvPg0KCQkJPHBhdGggZD0iTTAuMzYxLDE2LjEyNWwxMy42NjIsNS40NjVsMTIuNTM3LDUuMDE1YzAuNDc3LDAuMTkxLDEuMjUxLDAuMTkxLDEuNzI5LDBsMTIuNTQxLTUuMDE2bDEzLjY1OC01LjQ2Mw0KCQkJCWMwLjQ3Ny0wLjE5MSwwLjQ4LTAuNTExLDAuMDEtMC43MTZMMjguMjc3LDQuMDQ4Yy0wLjQ3MS0wLjIwNC0xLjIzNi0wLjIwNC0xLjcwOCwwTDAuMzUxLDE1LjQxDQoJCQkJQy0wLjEyMSwxNS42MTQtMC4xMTYsMTUuOTM1LDAuMzYxLDE2LjEyNXoiLz4NCgkJPC9nPg0KCTwvZz4NCjwvZz4NCjxnPg0KPC9nPg0KPGc+DQo8L2c+DQo8Zz4NCjwvZz4NCjxnPg0KPC9nPg0KPGc+DQo8L2c+DQo8Zz4NCjwvZz4NCjxnPg0KPC9nPg0KPGc+DQo8L2c+DQo8Zz4NCjwvZz4NCjxnPg0KPC9nPg0KPGc+DQo8L2c+DQo8Zz4NCjwvZz4NCjxnPg0KPC9nPg0KPGc+DQo8L2c+DQo8Zz4NCjwvZz4NCjwvc3ZnPg0K',
            100
        );
    }


    /**
     * Removes specific post types from the ACF post query.
     *
     * @param array $args The query arguments.
     * @param string $field The ACF field name.
     * @param int $id The post ID.
     * @return array The modified query arguments.
     */
    public function removeFromAcfPostQuery($args, $field, $id)
    {
        $args['post_type'] = array_filter($args['post_type'] ?? [], function($postType) {
            return strpos($postType, 'mod-') === false;
        });

        return $args;
    }

    /**
     * Updates the post_modified date on posts related to a specific module.
     *
     * @param int $postId The ID of the module post.
     * @param \WP_Post $postAfter The WP_Post object after the update.
     *
     * @return void
     */
    public function updatePostModifiedDateOnPostsRelatedToModule(int $postId, \WP_Post $post) {

        // Bail early if not a module
        if (!str_starts_with($post->post_type, 'mod-')) {
            return;
        }

        $updateDateOnPostsRelatedToModule = new Helper\UpdateDateOnPostsRelatedToModule(self::$moduleManager);
        $updateDateOnPostsRelatedToModule->update($post);
    }

    /**
     * Updates the post_modified date when modularity-modules post meta is updated
     *
     * @param int|array $metaId The meta ID.
     * @param int $postId The post ID.
     * @param string $metaKey The meta key.
     * @param mixed $metaValue The meta value.
     *
     * @return void
     */
    public function updatePostModifiedDateOnMetaUpdate(int|array $metaId, int $postId, string $metaKey, $metaValue) 
    {
        // Bail early if not an update of the modularity-modules
        if (!in_array($metaKey, ['modularity-modules']) || !is_array($metaValue)) {
            return;
        }

        // Unhook post_updated to prevent infinite loops
        remove_action('post_updated', [$this, 'updatePostModifiedDateOnPostsRelatedToModule'], 10, 2);
        // Update the current post
        wp_update_post([
            'ID' => $postId,
            'post_modified' => current_time('mysql'),
            'post_modified_gmt' => current_time('mysql', 1)
        ]);
        // Add the hook back once the post has been updated
        add_action('post_updated', [$this, 'updatePostModifiedDateOnPostsRelatedToModule'], 10, 2);

    }
}
