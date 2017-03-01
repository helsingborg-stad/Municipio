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

    /**
     * Search systems
     * @param  string $q Search query
     * @return array     Search results
     */
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

    public static function filter($systems, $filter = array(), $unitId = array())
    {
        // Only selectable systems
        if (in_array('selectable', $filter)) {
            $systems = array_filter($systems, function ($item) {
                return $item->selectable;
            });
        }

        // Only forced systems
        if (in_array('forced', $filter)) {
            $systems = array_filter($systems, function ($item) {
                return isset($item->forced) ? $item->forced : false;
            });
        }

        // Only systems selected by user
        if (in_array('user_only_selected', $filter)) {
            $selected = (array)get_user_meta(get_current_user_id(), 'user_systems', true);
            $systems = array_filter($systems, function ($item) use ($selected) {
                return in_array($item->id, $selected);
            });
        }

        // Systems avaiable to user (selected and non selected)
        if (in_array('user', $filter)) {
            $selected = get_user_meta(get_current_user_id(), 'user_systems', true);

            $systemForced = self::getUnitForced($unitId);
            $systemSelectable = self::getUnitSelectable($unitId);

            $systems = array_filter(self::getAll(), function ($system) use ($systemForced, $systemSelectable) {
                return in_array($system->id, $systemForced) || in_array($system->id, $systemSelectable);
            });

            // To check if the user ever has made any selections for his/her system list
            // we check if the selected systems is an empty string
            // - Empty string means the record is missing in the db
            // - Emtpy array means the record exist but is empty
            if ($selected === '') {
                $selected = array_filter($systems, function ($system) {
                    return $system->forced;
                });
            }

            // Cast selected to array
            $selected = (array) $selected;
            foreach ($systems as $system) {
                $system->selected = false;

                if (in_array($system->id, $selected)) {
                    $system->selected = true;
                }
            }
        }

        return $systems;
    }

    public static function getAvailabelSystems($unitId = null, $filter = array(), $onlyId = false)
    {
        $systems = self::getAll();

        if ($unitId) {
            if ($unitId === 'user') {
                $unitId = get_user_meta(get_current_user_id(), 'user_administration_unit', true);
            }

            $systems = self::getUnitSystems($unitId);
        }

        if ($filter) {
            $systems = self::filter($systems, $filter, $unitId);
        }

        if ($onlyId) {
            return array_keys($systems);
        }

        uasort($systems, function ($a, $b) {
            return strcmp($a->name, $b->name);
        });

        return $systems;
    }

    /**
     * Get a units selectable systems
     * @param  array $unitId Unit id
     * @return array
     */
    public static function getUnitSelectable($unitId)
    {
        $systemOptions = get_site_option('user-systems-options');
        $systemSelectable = array();

        foreach ((array) $unitId as $unit) {
            if (isset($systemOptions['selectable'][$unit])) {
                $systemSelectable = array_merge($systemSelectable, $systemOptions['selectable'][$unit]);
            }
        }

        return $systemSelectable;
    }

    /**
     * Get a units forced systems
     * @param  array $unitId Unit id
     * @return array
     */
    public static function getUnitForced($unitId)
    {
        $systemOptions = get_site_option('user-systems-options');
        $systems = array();

        foreach ((array) $unitId as $unit) {
            if (isset($systemOptions['forced'][$unit])) {
                $systems = array_merge($systems, $systemOptions['forced'][$unit]);
            }
        }

        return $systems;
    }

    /**
     * Only get systems for a specific administration unit id
     * @param  int $unitId
     * @return array
     */
    public function getUnitSystems($unitId)
    {
        $systems = self::getAll();
        $systemForced = self::getUnitForced($unitId);
        $systemSelectable = self::getUnitSelectable($unitId);

        foreach ($systems as $system) {
            $system->forced = false;
            if (in_array($system->id, $systemForced)) {
                $system->forced = true;
            }

            $system->selectable = false;
            if (in_array($system->id, $systemSelectable)) {
                $system->selectable = true;
            }
        }

        return $systems;
    }

    /**
     * Get all systems
     * @return array
     */
    public static function getAll()
    {
        $systems = array();

        global $wpdb;
        $query = "SELECT * FROM {$wpdb->base_prefix}" . self::$tableSuffix . " ORDER BY name ASC";
        $dbSystems = $wpdb->get_results($query);

        // If is on a local ip
        $unavailable = true;
        if (method_exists('\SsoAvailability\SsoAvailability', 'isSsoAvailable') && \SsoAvailability\SsoAvailability::isSsoAvailable()) {
            $unavailable = false;
        }

        foreach ($dbSystems as $system) {
            $system->unavailable = false;
            if ($system->is_local == 1 && $unavailable) {
                $system->unavailable = true;
            }

            $systems[$system->id] = $system;
        }

        ksort($systems);
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
