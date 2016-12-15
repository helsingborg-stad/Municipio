<?php

namespace Intranet\User;

class Systems
{
    public static $tableSuffix = 'systems';

    public function __construct()
    {
        if (!defined('MULTISITE') || !MULTISITE) {
            return;
        }

        add_action('admin_init', array($this, 'createDatabaseTable'));

        add_action('admin_menu', array($this, 'createManageSystemsPage'));
        add_action('admin_init', array($this, 'addSystem'));
        add_action('admin_init', array($this, 'saveSystems'));
        add_action('admin_init', array($this, 'editSystem'));
        add_action('admin_init', array($this, 'removeSystem'));
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
                $isEdit = false;
                if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
                    $isEdit = self::getSystem($_GET['edit']);
                }

                include INTRANET_PATH . 'templates/admin/systems/form.php';
            },
            'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI1MTIiIGhlaWdodD0iNTEyIiB2aWV3Qm94PSIwIDAgOTU2LjY5OSA5NTYuNjk5Ij48cGF0aCBkPSJNNzgyLjcgNDEzLjJoLS41Yy03LjctMTIxLjctMTA4LjktMjE4LTIzMi41LTIxOC0xMTQuMSAwLTIwOSA4Mi0yMjkuMSAxOTAuMi0yLjYtLjEtNS4zLS4yLTcuOS0uMi04NSAwLTE1Ni43IDU2LjMtMTgwLjEgMTMzLjYtMy42LS4zLTcuMy0uNS0xMS0uNUM1NC41IDUxOC4zIDAgNTcyLjcgMCA2MzkuOWMwIDY3LjIgNTQuNCAxMjEuNiAxMjEuNSAxMjEuNmg2NjEuMWM5Ni4yIDAgMTc0LjEtNzggMTc0LjEtMTc0LjEwMiAwLTk2LjEtNzcuOC0xNzQuMi0xNzQtMTc0LjJ6IiBmaWxsPSIjRkZGIi8+PC9zdmc+',
            200
        );
    }

    /**
     * Removes a system when clicking the trash button
     * @return boolean
     */
    public function removeSystem()
    {
        if (!isset($_POST['manage-user-systems-remove']) || empty($_POST['manage-user-systems-remove'])) {
            return false;
        }

        $systemId = isset($_POST['manage-user-systems-remove']) ? sanitize_text_field($_POST['manage-user-systems-remove']) : null;
        if (!$systemId) {
            return false;
        }

        global $wpdb;
        $wpdb->delete($wpdb->base_prefix . self::$tableSuffix, array('id' => $systemId), array('%d'));

        return true;
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

        update_site_option('user-systems-options', array(
            'selectable' => isset($_POST['selectable']) ? $_POST['selectable'] : array(),
            'forced' => isset($_POST['forced']) ? $_POST['forced'] : array()
        ));

        return;
    }

    public static function search($q)
    {
        $q = trim($q);

        if (!is_user_logged_in() || empty($q)) {
            return array();
        }

        $systems = \Intranet\User\Systems::getAvailabelSystems('user', array('user'));
        $systems = array_filter($systems, function ($item) use ($q) {
            return strpos(strtolower($item->name), strtolower($q)) > -1;
        });

        return $systems;
    }

    /**
     * Get a system
     * @param  integer    $id The system ID
     * @return stdObject      System data
     */
    public static function getSystem($id)
    {
        global $wpdb;
        $query = $wpdb->prepare("SELECT * FROM {$wpdb->base_prefix}" . self::$tableSuffix . " WHERE id = %d ORDER BY name ASC", array($id));
        return $wpdb->get_row($query);
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
            $systemForced = array();
            $systemSelectable = array();

            foreach ((array) $unitId as $unit) {
                if (isset($systemOptions['forced'][$unit])) {
                    $systemForced = array_merge($systemForced, $systemOptions['forced'][$unit]);
                }

                if (isset($systemOptions['selectable'][$unit])) {
                    $systemSelectable = array_merge($systemSelectable, $systemOptions['selectable'][$unit]);
                }
            }

            foreach ($systems as $system) {
                $system->forced = false;
                $system->unavailable = false;

                if (in_array($system->id, $systemForced)) {
                    $system->forced = true;
                }

                $system->selectable = false;
                if (in_array($system->id, $systemSelectable)) {
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
                return isset($item->forced) ? $item->forced : false;
            });

            $systems = array_merge($systems, $selectable);
        }

        if (in_array('only_selected', $filter)) {
            $selected = (array)get_user_meta(get_current_user_id(), 'user_systems', true);
            $systems = array_filter($allSystems, function ($item) use ($selected) {
                return in_array($item->id, $selected);
            });
        }

        if (in_array('user', $filter)) {
            $selected = (array)get_user_meta(get_current_user_id(), 'user_systems', true);
            $systems = array_filter($allSystems, function ($item) use ($selected) {
                return in_array($item->id, $selected);
            });

            $systems = array_merge($systems, self::getAvailabelSystems($unitId, array('forced')));
        }

        $systems = array_map('unserialize', array_unique(array_map('serialize', $systems)));

        // If is on a local ip, return all system
        if (method_exists('\SsoAvailability\SsoAvailability', 'isSsoAvailable') && \SsoAvailability\SsoAvailability::isSsoAvailable()) {
            return $systems;
        }

        foreach ($systems as $system) {
            if (!$system->is_local) {
                continue;
            }

            $system->unavailable = true;
        }

        uasort($systems, function ($a, $b) {
            return $a->unavailable > $b->unavailable;
        });

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
        $isLocal = isset($_POST['system-is-local']) ? 1 : 0;

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
                'description' => $description,
                'is_local' => $isLocal
            ),
            array(
                '%s',
                '%s',
                '%s',
                '%d'
            )
        );
    }

    public function editSystem()
    {
        if (!isset($_POST['system-manager-edit-system']) || !is_numeric($_POST['system-manager-edit-system'])) {
            return false;
        }

        $systemId = sanitize_text_field($_POST['system-manager-edit-system']);

        $data = array();

        if (isset($_POST['system-name']) && !empty($_POST['system-name'])) {
            $data['name'] = sanitize_text_field($_POST['system-name']);
        }

        if (isset($_POST['system-url']) && !empty($_POST['system-url'])) {
            $data['url'] = sanitize_text_field($_POST['system-url']);
        }

        if (isset($_POST['system-description']) && !empty($_POST['system-description'])) {
            $data['description'] = sanitize_text_field($_POST['system-description']);
        }

        $isLocal = isset($_POST['system-is-local']) ? 1 : 0;
        $data['is_local'] = $isLocal;

        global $wpdb;

        $wpdb->update(
            $wpdb->base_prefix . self::$tableSuffix,
            $data,
            array(
                'id' => $systemId
            )
        );

        wp_redirect(admin_url('admin.php?page=user-systems'));
        exit;
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
            is_local smallint(1) DEFAULT 0,
            UNIQUE KEY id (id)
        ) $charsetCollation;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

        update_site_option('systems-db-version', '1.0.0');

        restore_current_blog();
    }
}
