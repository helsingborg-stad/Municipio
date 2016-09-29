<?php

namespace Intranet\User;

class AdministrationUnits
{
    public static $tableSuffix = 'administration_units';

    public function __construct()
    {
        if (!defined('MULTISITE') || !MULTISITE) {
            return;
        }

        add_action('admin_init', array($this, 'createDatabaseTable'));

        add_action('network_admin_menu', array($this, 'createAdministrationUnitsAdminPage'));
        add_action('admin_init', array($this, 'addAdministrationUnit'));
        add_action('admin_init', array($this, 'removeAdministrationUnit'));
    }

    public function createAdministrationUnitsAdminPage()
    {
        add_menu_page(
            __('Administration units', 'municipio-intranet'),
            __('Administration units', 'municipio-intranet'),
            'manage_network',
            'administration-units',
            function () {
                include INTRANET_PATH . 'templates/admin/administration-units/form.php';
            },
            'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI1MTIiIGhlaWdodD0iNTEyIiB2aWV3Qm94PSIwIDAgNDcuMDAzIDQ3LjAwNCI+PHBhdGggZD0iTTQwLjYxIDQyLjk1NlY0MS45NGExLjEyIDEuMTIgMCAwIDAtLjYzNi0xLjAxYy0uMjQ4LS4xMTgtNS45NzgtMi43OTItMTUuMzU0LTIuOTk3VjM0LjExYTIuMjI3IDIuMjI3IDAgMCAwIDEuMTItMS45Mjd2LTEuMjM1aDcuOTU4YTIuMjQgMi4yNCAwIDAgMCAyLjE3LTIuNzc4YzEuOTUzLS42NDUgMi45OC0xLjIzIDMuMDctMS4yODQuMzQyLS4yLjU1My0uNTY3LjU1My0uOTY2VjE5LjhoMS4wNjJhMS4xMTggMS4xMTggMCAwIDAgMC0yLjIzOGgtNC4zNmExLjExOCAxLjExOCAwIDAgMCAwIDIuMjM4aDEuMDYzdjUuNDNjLS41NDguMjUtMS41MDMuNjQ0LTIuODUgMS4wNDJsLS43NTctLjc1OGEyLjI0IDIuMjQgMCAwIDAtMS45NTctLjYyNWMtLjAyNC4wMDMtMi4yNTUuMzctNS45NS40ODZWMjMuNDJjNC4wNzQtLjI1MiA2LjE0Ni0xLjEzMiA2LjQwMi0xLjI0OGEyLjIzOCAyLjIzOCAwIDAgMCAxLjMwMy0yLjI2M0wzMS43MyAyLjg3YTIuMjQgMi4yNCAwIDAgMC0xLjM1Ny0xLjg0QzMwLjEyLjkyOCAyNy43OTUgMCAyMy41IDBzLTYuNjE4LjkyNy02Ljg3IDEuMDMzYTIuMjM2IDIuMjM2IDAgMCAwLTEuMzU3IDEuODRsLTEuNzE1IDE3LjA0YTIuMjM2IDIuMjM2IDAgMCAwIDEuMzA0IDIuMjYzYy4yNTUuMTE1IDIuMzI3Ljk5NiA2LjQgMS4yNXYxLjk1NGMtMy42OTQtLjExNi01LjkyNC0uNDgtNS45NDgtLjQ4N2EyLjIzIDIuMjMgMCAwIDAtMS45Ni42MjVsLS43NTYuNzU4Yy0xLjM0OC0uMzk4LTIuMzAzLS43OS0yLjg1LTEuMDQzdi01LjQyOGgxLjA2MmExLjExOCAxLjExOCAwIDAgMCAwLTIuMjM4SDYuNDVhMS4xMTggMS4xMTggMCAwIDAgMCAyLjIzOGgxLjA2djYuMTJjMCAuMzk2LjIxMi43NjYuNTU0Ljk2Ny4wOTUuMDU1IDEuMTI2LjY0NiAzLjEgMS4yOTNhMi4yMiAyLjIyIDAgMCAwIC4wOTUgMS4zODcgMi4yNCAyLjI0IDAgMCAwIDIuMDY3IDEuMzhoNy45Mzd2MS4yMzdjMCAuODI1LjQ1MiAxLjUzOCAxLjEyIDEuOTI3djMuODIyYy05LjM3OC4yMDItMTUuMTA2IDIuODc2LTE1LjM1NSAyLjk5NS0uMzkuMTg3LS42MzcuNTgtLjYzNyAxLjAxdjEuMDE3YTIuMTczIDIuMTczIDAgMCAwLTEuMDYgMS44NjMgMi4xOCAyLjE4IDAgMCAwIDQuMzYgMCAyLjE3IDIuMTcgMCAwIDAtMS4wNjItMS44NjN2LS4yNzRjMS42ODItLjY2OCA2LjYwOC0yLjM1IDEzLjc1My0yLjUxM3YyLjc4N2EyLjE3MyAyLjE3MyAwIDAgMC0xLjA2IDEuODYzIDIuMTggMi4xOCAwIDAgMCA0LjM2IDAgMi4xNyAyLjE3IDAgMCAwLTEuMDYyLTEuODYzdi0yLjc4N2M3LjEyLjE2NSAxMi4wNjQgMS44NDcgMTMuNzUzIDIuNTEzdi4yNzRhMi4xNzIgMi4xNzIgMCAwIDAtMS4wNjIgMS44NjMgMi4xOCAyLjE4IDAgMSAwIDMuMy0xLjg3ek0xOS45MjggNi40NjRjLjA1LS4wMiAxLjI3LS41MjUgMy41NzQtLjUyNSAyLjMwNiAwIDMuNTI0LjUwMyAzLjU3NS41MjRhLjU2LjU2IDAgMCAxLS40MzYgMS4wM2MtLjAxLS4wMDMtMS4wODUtLjQzNi0zLjE0LS40MzZzLTMuMTMuNDMzLTMuMTQyLjQzOGEuNTYuNTYgMCAwIDEtLjQzLTEuMDMyem0tLjU2IDMuMzk3Yy4wNi0uMDIzIDEuNDYyLS42MDMgNC4xMzUtLjYwM3M0LjA3Ni41OCA0LjEzNC42MDRjLjI4NS4xMjIuNDIuNDUuMy43MzRhLjU2Ni41NjYgMCAwIDEtLjczNS4yOThjLS4wMS0uMDA0LTEuMjc3LS41MTctMy42OTgtLjUxN3MtMy42ODguNTEyLTMuNy41MThhLjU2LjU2IDAgMCAxLS40MzQtMS4wMzJ6bTQuMTM0IDMuODMyYy0yLjc4NiAwLTQuMjQ1LjU5LTQuMjYuNTk3YS41Ni41NiAwIDAgMS0uNDMzLTEuMDNjLjA2Ni0uMDMgMS42NTUtLjY4NyA0LjY5NC0uNjg3czQuNjMuNjU3IDQuNjk0LjY4NWMuMjgzLjEyLjQxNi40NDYuMy43M2EuNTY0LjU2NCAwIDAgMS0uNzM1LjNjLS4wMTItLjAwNC0xLjQ3LS41OTYtNC4yNi0uNTk2eiIgZmlsbD0iI0ZGRiIvPjwvc3ZnPg==',
            100
        );
    }

    public function removeAdministrationUnit()
    {
        if (!isset($_POST['administration-unit-delete']) || !is_numeric($_POST['administration-unit-delete'])) {
            return;
        }

        global $wpdb;
        $id = sanitize_text_field($_POST['administration-unit-delete']);

        $wpdb->delete($wpdb->base_prefix . self::$tableSuffix, array('id' => $id), array('%d'));
        return;
    }

    /**
     * Get a list of all systems in the database
     * @return arry Systems
     */
    public static function getAdministrationUnits()
    {
        global $wpdb;
        return $wpdb->get_results("SELECT * FROM {$wpdb->base_prefix}" . self::$tableSuffix . " ORDER BY name ASC");
    }

    /**
     * Get a list of all systems in the database
     * @return arry Systems
     */
    public static function getAdministrationUnit($id)
    {
        global $wpdb;
        $query = $wpdb->prepare("SELECT name FROM {$wpdb->base_prefix}" . self::$tableSuffix . " WHERE id = %d ORDER BY name ASC", array($id));
        return $wpdb->get_var($query);
    }

    public static function getAdministrationUnitIdFromString($string)
    {
        global $wpdb;
        $string = strtolower($string);
        $query = $wpdb->prepare("SELECT id FROM {$wpdb->base_prefix}" . self::$tableSuffix . " WHERE LCASE(name) = %s ORDER BY name ASC", array($string));
        return $wpdb->get_var($query);
    }

    /**
     * Adds a system to list of available systems
     */
    public function addAdministrationUnit()
    {
        if (!isset($_POST['administration-unit-add'])) {
            return;
        }

        global $wpdb;

        $name = sanitize_text_field($_POST['administration-unit-name']);

        if (empty($name)) {
            add_action('admin_notices', function () {
                echo '<div class="notice notice-success is-dismissible">
                           <p>' . __('All fields must be filled in to add a new administration unit', 'municipio-intranet') . '</p>
                      </div>';
            });

            return;
        }

        self::insertAdministrationUnit($name);
    }

    public static function insertAdministrationUnit($name)
    {
        global $wpdb;

        $query = $wpdb->prepare("
            INSERT IGNORE INTO " . $wpdb->base_prefix . self::$tableSuffix . "
            (name) VALUES (%s)
        ", $name);

        $wpdb->query($query);

        return $wpdb->insert_id;
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

        if (!empty(get_site_option('administration-units-db-version')) && $wpdb->get_var("SHOW TABLES LIKE '$tableName'") == $tableName) {
            restore_current_blog();
            return;
        }

        $sql = "CREATE TABLE $tableName (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            name varchar(55) DEFAULT '' NOT NULL,
            UNIQUE KEY id (id),
            UNIQUE (name)
        ) $charsetCollation;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

        update_site_option('administration-units-db-version', '1.0.0');

        restore_current_blog();
    }
}
