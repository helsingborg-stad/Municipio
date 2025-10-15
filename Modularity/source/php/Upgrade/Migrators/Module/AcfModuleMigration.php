<?php

namespace Modularity\Upgrade\Migrators\Module;

use Modularity\Upgrade\Migrators\Module\AcfModuleMigrationHandler;
use WP_CLI;

class AcfModuleMigration {

    private $db;
    private $modules;
    private $fields;
    private $newModuleName;

    public function __construct(\wpdb $db, array $modules, array $fields, $newModuleName = false) {
        $this->db = $db;
        $this->modules = $modules;
        $this->fields = $fields;
        $this->newModuleName = $newModuleName;
    }

    public function migrateModules() 
    {
        if (!$this->isValidParams()) {
            WP_CLI::warning('Empty Modules, Fields or no Database');
            return false;
        }

        foreach ($this->modules as &$module) {
            if (!$module->ID) {
                WP_CLI::warning('No module ID');
                continue;
            }
            
            $migrationFieldManager = new AcfModuleMigrationHandler($this->fields, $module->ID);
            $migrationFieldManager->migrateModuleFields();
            //Update post type
            if (!empty($this->newModuleName)) {
                $this->updateModuleName($module);
            }
        }
    }

    private function updateModuleName($module) {
        $QueryUpdatePostType = $this->db->prepare(
            "UPDATE " . $this->db->posts . " SET post_type = %s WHERE ID = %d", 
            $this->newModuleName, 
            $module->ID
        );

        $successfullyUpdatedName = $this->db->query($QueryUpdatePostType); 

        if (!$successfullyUpdatedName) {
            WP_CLI::warning(sprintf('Failed to update post type for module with ID %s', (string) $module->ID));
            return;
        }

        WP_CLI::line(sprintf('Module post type updated from %s to %s', (string) $module->post_type, (string) $this->newModuleName));
    }

    private function isValidParams() {
        return 
            !empty($this->modules) &&
            !empty($this->fields) && 
            !empty($this->db);
    }
}