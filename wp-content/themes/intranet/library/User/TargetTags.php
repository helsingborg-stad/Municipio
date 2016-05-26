<?php

namespace Intranet\User;

class TargetTags
{
    public function __construct()
    {
        add_action('init', array($this, 'createDatabaseTable'));
        add_action('network_admin_menu', array($this, 'createManageTargetTagsPage'));

        add_action('admin_init', array($this, 'saveTags'));
    }

    public function createManageTargetTagsPage()
    {
        add_menu_page(
            __('Target tags', 'municipio-intranet'),
            __('Target tags', 'municipio-intranet'),
            'manage_network',
            'target-tags',
            array($this, 'manageTagsForm'),
            'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCA0ODYuOTgyIDQ4Ni45ODIiIHdpZHRoPSI1MTIiIGhlaWdodD0iNTEyIj48cGF0aCBkPSJNMTMxLjM1IDQyMi45N2MxNC42IDE0LjYgMzguMyAxNC42IDUyLjkgMGwxODEuMS0xODEuMWM1LjItNS4yIDkuMi0xMS40IDExLjgtMTggMTguMiA1LjEgMzUuOSA3LjggNTEuNSA3LjcgMzguNi0uMiA1MS40LTE3LjEgNTUuNi0yNy4yIDQuMi0xMCA3LjItMzEtMTkuOS01OC42bC0uOS0uOWMtMTYuOC0xNi44LTQxLjItMzIuMy02OC45LTQzLjgtNS4xLTIuMS0xMC4yLTQtMTUuMi01Ljh2LS4zYy0uMy0yMi4yLTE4LjItNDAuMS00MC40LTQwLjRsLTEwOC41LTEuNWMtMTQuNC0uMi0yOC4yIDUuNC0zOC4zIDE1LjZsLTE4MS4yIDE4MS4xYy0xNC42IDE0LjYtMTQuNiAzOC4zIDAgNTIuOWwxMjAuNCAxMjAuM3ptMTM5LjYtMzA1LjFjMTIuMS0xMi4xIDMxLjctMTIuMSA0My44IDAgNy4yIDcuMiAxMC4xIDE3LjEgOC43IDI2LjQgMTEuOSA4LjQgMjYuMSAxNi4yIDQxLjMgMjIuNSA1LjQgMi4yIDEwLjYgNC4yIDE1LjYgNS45bC0uNi00My42Yy45LjQgMS43LjcgMi42IDEuMSAyMy43IDkuOSA0NSAyMy4zIDU4LjcgMzdsLjYuNmMxMyAxMy4zIDE0LjQgMjEuOCAxMy4zIDI0LjQtMy40IDguMS0zOS45IDE1LjMtOTUuMy03LjgtMTYuMi02LjgtMzEuNC0xNS4yLTQzLjctMjQuMy0uNC41LS45IDEtMS4zIDEuNS0xMi4xIDEyLjEtMzEuNyAxMi4xLTQzLjggMC0xMi0xMi0xMi0zMS42LjEtNDMuN3oiIGZpbGw9IiNGRkYiLz48L3N2Zz4=',
            100
        );
    }

    public function manageTagsForm()
    {
        include_once INTRANET_PATH . 'templates/admin/target-tags/form.php';
    }

    public function saveTags()
    {
        if (!isset($_POST['manage-target-tags-action'])) {
            return;
        }

        global $wpdb;

        $values = array();
        $tags = self::getAvailableTags();
        $postedTags = $_POST['tag-manager-tags'];

        // Remove removed tags
        $removeTags = array_filter($tags, function ($item) use ($postedTags) {
            return !in_array($item->tag, $postedTags);
        });

        foreach ($removeTags as $tag) {
            $wpdb->delete($wpdb->prefix . 'target_tags', array('id' => $tag->id));
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

        $query = "INSERT INTO {$wpdb->prefix}target_tags (tag) VALUES ";
        $query .= implode( ",\n", $values);

        $wpdb->query($query);

        exit;
    }

    public static function getAvailableTags()
    {
        global $wpdb;
        global $current_site;

        switch_to_blog($current_site->blog_id);

        $tags = $wpdb->get_results("SELECT id, tag FROM {$wpdb->prefix}target_tags ORDER BY tag ASC");

        restore_current_blog();

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
        $tableName = $wpdb->prefix . 'target_tags';

        if (!empty(get_site_option('taget-tags-db-version')) && $wpdb->get_var("SHOW TABLES LIKE '$tableName'") == $tableName) {
            restore_current_blog();
            return;
        }

        $sql = "CREATE TABLE $tableName (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            tag varchar(55) DEFAULT '' NOT NULL,
            UNIQUE KEY id (id)
        ) $charsetCollation;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

        update_site_option('taget-tags-db-version', '1.0.0');

        restore_current_blog();
    }
}
