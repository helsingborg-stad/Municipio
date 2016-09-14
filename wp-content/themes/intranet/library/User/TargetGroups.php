<?php

namespace Intranet\User;

class TargetGroups
{
    public static $tableSuffix = 'target_groups';

    public function __construct()
    {
        if (!defined('MULTISITE') || !MULTISITE) {
            return;
        }

        add_action('init', array($this, 'createDatabaseTable'));

        // Manage target groups (admin)
        add_action('network_admin_menu', array($this, 'createManageTargetGroupsPage'));
        add_action('admin_init', array($this, 'saveGroups'));

        // Restrict posts to target groups
        add_action('add_meta_boxes', array($this, 'addRestrictionMetabox'));
        add_action('save_post', array($this, 'saveGroupRestrictions'));

        // Limit access to posts based on groups
        add_action('pre_get_posts', array($this, 'doGroupRestriction'));

        // Restruct modules
        add_filter('Modularity/Display/Markup', array($this, 'restrictModules'), 10, 2);

        // Shortcode
        add_shortcode('target', array($this, 'shortcodeTarget'));

        // Editor shortcode button
        add_action('admin_init', array($this, 'initTargetedContentButton'));
    }

    /**
     * Restrict access for modules
     * @param  string $markup Module markup
     * @param  object $module Module post object
     * @return string         New markup
     */
    public function restrictModules($markup, $module)
    {
        $groups = get_post_meta($module->ID, '_target_groups', true);

        if (!self::userInGroup($groups, get_current_user_id())) {
            return '';
        }

        return $markup;
    }

    /**
     * Target parts of content to group with shortcode
     * @param  array $args     Arguments
     * @param  string $content The content
     * @return string          Viewable content
     */
    public function shortcodeTarget($args, $content = null)
    {
        $groups = isset($args[0]) ? explode(',', $args[0]) : null;

        if (\Intranet\User\TargetGroups::userInGroup($groups)) {
            return $content;
        }

        return '';
    }

    /**
     * Checks if a userId is in a member of a given group or given groups
     * @param  integer|array $group  Group id or group ids
     * @param  integer       $userId The user id to check
     * @return boolean
     */
    public static function userInGroup($group, $userId = null)
    {
        if (is_null($group) || empty($group)) {
            return true;
        }

        if (is_null($userId)) {
            $userId = get_current_user_id();
        }

        $userGroups = self::getGroups($userId);

        // Check if any match if grop is an array
        if (is_array($group)) {
            return count(array_intersect($group, $userGroups)) > 0;
        }

        // Check if single match
        return in_array($group, $userGroups);
    }

    /**
     * Do target group restrictions in the pre get posts hook
     * Be aware of that this hook will not fire on get_post, get_blog_post
     * @param  [type] $query [description]
     * @return [type]        [description]
     */
    public function doGroupRestriction($query)
    {
        if (is_admin()) {
            return;
        }

        $groups = self::getGroups();

        $metaQuery = $query->get('meta_query');

        $metaQuery[] = array(
            'relation' => 'OR',
            array(
                'key' => '_target_groups',
                'value' => '.*;s:[0-9]+:"(' . implode('|', $groups) . ')".*',
                'compare' => 'REGEXP',
            ),
            array(
                'key' => '_target_groups',
                'compare' => 'NOT EXISTS'
            )
        );

        $query->set('meta_query', $metaQuery);
    }

    /**
     * Get groups of a user
     * @param  integer $userId User id
     * @return array           User groups
     */
    public static function getGroups($userId = null)
    {
        if (!$userId) {
            $userId = get_current_user_id();
        }

        $groups = get_user_meta($userId, 'user_target_groups', true);

        if (!$groups) {
            $groups = array();
        }

        $groups[] = 'unit-' . get_user_meta($userId, 'user_administration_unit', true);

        return $groups;
    }

    /**
     * Adds restriction metabox to admin post edit
     */
    public function addRestrictionMetabox()
    {
        global $current_screen;
        global $post;

        $action = $current_screen->action;

        if (empty($action)) {
            $action = (isset($_GET['action']) && !empty($_GET['action'])) ? $_GET['action'] : null;
        }

        $validAction = in_array($action, array(
            'add',
            'edit'
        ));

        if (!$validAction) {
            return;
        }

        add_meta_box(
            'target-group',
            __('Restrict to target groups', 'municipio-intranet'),
            function () {
                include INTRANET_PATH . 'templates/admin/target-groups/restrict.php';
            },
            null,
            'advanced',
            'default'
        );
    }

    /**
     * Saves group restrictions
     * @param  integer $postId Post id
     * @return void
     */
    public function saveGroupRestrictions($postId)
    {
        if (!isset($_POST['target_groups']) || !is_array($_POST['target_groups'])) {
            delete_post_meta($postId, '_target_groups');
            return;
        }

        update_post_meta($postId, '_target_groups', $_POST['target_groups']);
    }

    /**
     * Sets up the manage groups page
     * @return void
     */
    public function createManageTargetGroupsPage()
    {
        add_menu_page(
            __('Target groups', 'municipio-intranet'),
            __('Target groups', 'municipio-intranet'),
            'manage_network',
            'target-groups',
            function () {
                include INTRANET_PATH . 'templates/admin/target-groups/form.php';
            },
            'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCA0ODYuOTgyIDQ4Ni45ODIiIHdpZHRoPSI1MTIiIGhlaWdodD0iNTEyIj48cGF0aCBkPSJNMTMxLjM1IDQyMi45N2MxNC42IDE0LjYgMzguMyAxNC42IDUyLjkgMGwxODEuMS0xODEuMWM1LjItNS4yIDkuMi0xMS40IDExLjgtMTggMTguMiA1LjEgMzUuOSA3LjggNTEuNSA3LjcgMzguNi0uMiA1MS40LTE3LjEgNTUuNi0yNy4yIDQuMi0xMCA3LjItMzEtMTkuOS01OC42bC0uOS0uOWMtMTYuOC0xNi44LTQxLjItMzIuMy02OC45LTQzLjgtNS4xLTIuMS0xMC4yLTQtMTUuMi01Ljh2LS4zYy0uMy0yMi4yLTE4LjItNDAuMS00MC40LTQwLjRsLTEwOC41LTEuNWMtMTQuNC0uMi0yOC4yIDUuNC0zOC4zIDE1LjZsLTE4MS4yIDE4MS4xYy0xNC42IDE0LjYtMTQuNiAzOC4zIDAgNTIuOWwxMjAuNCAxMjAuM3ptMTM5LjYtMzA1LjFjMTIuMS0xMi4xIDMxLjctMTIuMSA0My44IDAgNy4yIDcuMiAxMC4xIDE3LjEgOC43IDI2LjQgMTEuOSA4LjQgMjYuMSAxNi4yIDQxLjMgMjIuNSA1LjQgMi4yIDEwLjYgNC4yIDE1LjYgNS45bC0uNi00My42Yy45LjQgMS43LjcgMi42IDEuMSAyMy43IDkuOSA0NSAyMy4zIDU4LjcgMzdsLjYuNmMxMyAxMy4zIDE0LjQgMjEuOCAxMy4zIDI0LjQtMy40IDguMS0zOS45IDE1LjMtOTUuMy03LjgtMTYuMi02LjgtMzEuNC0xNS4yLTQzLjctMjQuMy0uNC41LS45IDEtMS4zIDEuNS0xMi4xIDEyLjEtMzEuNyAxMi4xLTQzLjggMC0xMi0xMi0xMi0zMS42LjEtNDMuN3oiIGZpbGw9IiNGRkYiLz48L3N2Zz4=',
            100
        );
    }

    /**
     * Saves the groups created/removed in admin
     * @return void
     */
    public function saveGroups()
    {
        if (!isset($_POST['manage-target-tags-action'])) {
            return;
        }

        global $wpdb;

        $values = array();
        $tags = self::getAvailableGroups();
        $postedTags = $_POST['tag-manager-tags'];

        // Remove removed tags
        $removeTags = array_filter($tags, function ($item) use ($postedTags) {
            return !in_array($item->tag, $postedTags);
        });

        foreach ($removeTags as $tag) {
            $wpdb->delete($wpdb->prefix . self::$tableSuffix, array('id' => $tag->id));
        }

        // Add new tags
        $addTags = array();
        foreach ($tags as $tag) {
            $addTags[] = $tag->tag;
        }

        $addTags = array_diff($postedTags, $addTags);

        foreach ($addTags as $tag) {
            $values[] = $wpdb->prepare("(%s)", $tag);
        }

        $query = "INSERT INTO {$wpdb->prefix}" . self::$tableSuffix . " (tag) VALUES ";
        $query .= implode( ",\n", $values);

        $wpdb->query($query);
    }

    /**
     * Get available groups
     * @return array Groups
     */
    public static function getAvailableGroups()
    {
        global $wpdb;
        global $current_site;

        switch_to_blog($current_site->blog_id);

        $tags = $wpdb->get_results("SELECT id, tag FROM {$wpdb->prefix}" . self::$tableSuffix . " ORDER BY tag ASC");

        restore_current_blog();

        $units = \Intranet\User\AdministrationUnits::getAdministrationUnits();
        foreach ($units as &$unit) {
            $unit->tag = $unit->name;
            unset($unit->name);

            $unit->id = 'unit-' . $unit->id;
        }

        $tags = array_merge($tags, $units);

        uasort($tags, function ($a, $b) {
            return $a->tag > $b->tag;
        });

        return $tags;
    }

    /**
     * Creates the database table for storing of the tags (if not already exists)
     * @return void
     */
    public function createDatabaseTable()
    {
        global $wpdb;
        global $current_site;

        switch_to_blog($current_site->blog_id);

        $charsetCollation = $wpdb->get_charset_collate();
        $tableName = $wpdb->prefix . self::$tableSuffix;

        if (!empty(get_site_option('taget-groups-db-version')) && $wpdb->get_var("SHOW TABLES LIKE '$tableName'") == $tableName) {
            restore_current_blog();
            return;
        }

        $sql = "CREATE TABLE $tableName (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            tag varchar(55) DEFAULT '' NOT NULL,
            UNIQUE KEY id (id)
        ) $charsetCollation;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

        update_site_option('taget-groups-db-version', '1.0.0');

        restore_current_blog();
    }

    public function initTargetedContentButton()
    {
        global $pagenow;

        if (!current_user_can('edit_posts') || !current_user_can('edit_pages') || $pagenow != 'post.php') {
            return;
        }

        add_action('admin_head', array($this, 'mceButtonHelp'));
        add_filter('mce_external_plugins', array($this, 'registerMcePlugin'));
        add_filter('mce_buttons', array($this, 'registerMceButton'));
    }

    public function registerMcePlugin($plugins)
    {
        $plugins['targeted_content'] = get_stylesheet_directory_uri() . '/assets/dist/js/mce-targeted-content.js';
        return $plugins;
    }

    public function registerMceButton($buttons)
    {
        array_push($buttons, 'targeted_content');
        return $buttons;
    }

    public function mceButtonHelp()
    {
        $groups = self::getAvailableGroups();
        echo "<script>var mce_target_content_groups = [";

            foreach ($groups as $group) {
                echo "{id: '{$group->id}', tag: '{$group->tag}'},";
            }

        echo "]</script>";
    }
}
