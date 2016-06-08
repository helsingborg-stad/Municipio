<?php

namespace Intranet\User;

class Systems
{
    public static $tableSuffix = 'systems';

    public function __construct()
    {
        add_action('init', array($this, 'createDatabaseTable'));

        add_action('admin_menu', array($this, 'createManageSystemsPage'));
        add_action('admin_init', array($this, 'addSystem'));
        add_action('admin_init', array($this, 'saveSystems'));
    }

    /**
     * Sets up the manage groups page
     * @return void
     */
    public function createManageSystemsPage()
    {
        add_menu_page(
            __('User systems', 'municipio-intranet'),
            __('User systems', 'municipio-intranet'),
            'manage_systems',
            'user-systems',
            function () {
                include INTRANET_PATH . 'templates/admin/systems/form.php';
            },
            'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI1MTIiIGhlaWdodD0iNTEyIiB2aWV3Qm94PSIwIDAgOTU2LjY5OSA5NTYuNjk5Ij48cGF0aCBkPSJNNzgyLjcgNDEzLjJoLS41Yy03LjctMTIxLjctMTA4LjktMjE4LTIzMi41LTIxOC0xMTQuMSAwLTIwOSA4Mi0yMjkuMSAxOTAuMi0yLjYtLjEtNS4zLS4yLTcuOS0uMi04NSAwLTE1Ni43IDU2LjMtMTgwLjEgMTMzLjYtMy42LS4zLTcuMy0uNS0xMS0uNUM1NC41IDUxOC4zIDAgNTcyLjcgMCA2MzkuOWMwIDY3LjIgNTQuNCAxMjEuNiAxMjEuNSAxMjEuNmg2NjEuMWM5Ni4yIDAgMTc0LjEtNzggMTc0LjEtMTc0LjEwMiAwLTk2LjEtNzcuOC0xNzQuMi0xNzQtMTc0LjJ6IiBmaWxsPSIjRkZGIi8+PC9zdmc+',
            200
        );
    }

    /**
     * Save system options
     * @return void
     */
    public function saveSystems()
    {
        if (!isset($_POST['manage-user-systems-action']) || isset($_POST['system-manager-add-system'])) {
            return;
        }

        if (isset($_POST['add-local-url-pattern']) && $_POST['add-local-url-pattern'] == 'true') {
            $this->savePattern();
            return;
        }

        if (isset($_POST['remove-local-url-pattern']) && !empty($_POST['remove-local-url-pattern'])) {
            $this->removePattern($_POST['remove-local-url-pattern']);
            return;
        }

        update_site_option('user-systems-options', array(
            'selectable' => isset($_POST['selectable']) ? $_POST['selectable'] : array(),
            'forced' => isset($_POST['forced']) ? $_POST['forced'] : array()
        ));

        return;
    }

    /**
     * Saves patterns
     * @return void
     */
    public function savePattern()
    {
        if (!isset($_POST['local-url-pattern']) || empty($_POST['local-url-pattern'])) {
            return;
        }

        $patterns = get_site_option('user_systems_ip_patterns', array());
        $patterns[] = sanitize_text_field($_POST['local-url-pattern']);

        update_site_option('user_systems_ip_patterns', $patterns);
    }

    /**
     * Removes a pattern
     * @param  string $pattern Pattern
     * @return void
     */
    public function removePattern($pattern)
    {
        $patterns = get_site_option('user_systems_ip_patterns', array());

        $patterns = array_filter($patterns, function ($item) use ($pattern) {
            return $item != stripslashes($pattern);
        });

        update_site_option('user_systems_ip_patterns', $patterns);
    }

    /**
     * Get a list of all systems in the database
     * @param  mixed (optional) $unitId        Unit id to get systems for
     * @return array                           Systems
     */
    public static function getAvailabelSystems($unitId = null, $filter = array())
    {
        global $wpdb;
        $query = "SELECT * FROM {$wpdb->base_prefix}" . self::$tableSuffix . " ORDER BY name ASC";

        $systems = $wpdb->get_results($query);

        if ($unitId) {
            switch ($unitId) {
                case 'user':
                    $unitId = get_user_meta(get_current_user_id(), 'user_administration_unit', true);
                    break;
            }

            $systemOptions = get_site_option('user-systems-options');

            foreach ($systems as $system) {
                $system->forced = false;

                if (isset($systemOptions['forced'][$unitId]) && in_array($system->id, (array)$systemOptions['forced'][$unitId])) {
                    $system->forced = true;
                }

                $system->selectable = false;
                if (isset($systemOptions['selectable'][$unitId]) && in_array($system->id, (array)$systemOptions['selectable'][$unitId])) {
                    $system->selectable = true;
                }
            }
        }

        if (count($filter) === 0) {
            return $systems;
        }

        // Filters
        $allSystems = $systems;
        $systems = array();

        if (in_array('selectable', $filter)) {
            $selectable = array_filter($allSystems, function ($item) {
                return $item->selectable;
            });

            $systems = array_merge($systems, $selectable);
        }

        if (in_array('forced', $filter)) {
            $selectable = array_filter($allSystems, function ($item) {
                return $item->forced;
            });

            $systems = array_merge($systems, $selectable);
        }

        if (in_array('only_selected', $filter)) {
            $selected = (array)get_user_meta(get_current_user_id(), 'user_systems', true);
            $systems = array_filter($allSystems, function ($item) use ($selected) {
                return in_array($item->id, $selected);
            });
        }

        return $systems;
    }

    /**
     * Adds a system to list of available systems
     */
    public function addSystem()
    {
        if (!isset($_POST['system-manager-add-system'])) {
            return;
        }

        global $wpdb;

        $name = sanitize_text_field($_POST['system-name']);
        $url = sanitize_text_field($_POST['system-url']);
        $description = sanitize_text_field($_POST['system-description']);

        if (empty($name) || empty($url) || empty($description)) {
            add_action('admin_notices', function () {
                echo '<div class="notice notice-success is-dismissible">
                           <p>' . __('All fields must be filled in to add a new system', 'municipio-intranet') . '</p>
                      </div>';
            });

            return;
        }

        $wpdb->insert(
            $wpdb->base_prefix . self::$tableSuffix,
            array(
                'name' => $name,
                'url' => rtrim($url, '/'),
                'description' => $description
            ),
            array(
                '%s',
                '%s',
                '%s'
            )
        );
    }

    /**
     * Creates the database table structure
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
            name varchar(255) DEFAULT '' NOT NULL,
            url varchar(255) DEFAULT '' NOT NULL,
            description longtext DEFAULT NULL,
            UNIQUE KEY id (id)
        ) $charsetCollation;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

        update_site_option('systems-db-version', '1.0.0');

        restore_current_blog();
    }
}
