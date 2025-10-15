<?php

namespace Modularity\Upgrade\Migrators\Module;

use Modularity\Upgrade\Migrators\MigratorInterface;
use WP_CLI;

class AcfModuleRemoveFieldMigrator implements MigratorInterface {

    private $fieldName;
    private $moduleId;

    public function __construct($fieldName, $moduleId) {
        $this->fieldName = $fieldName;
        $this->moduleId = $moduleId;
    }

    public function migrate():mixed {
        $deleted = delete_field($this->fieldName, $this->moduleId);

        if($deleted) {
            WP_CLI::line(sprintf('Deleting field %s in %s', (string) $this->fieldName, (string) $this->moduleId));
        } else {
            WP_CLI::warning(sprintf('Failed to delete field %s in %s', (string) $this->fieldName, (string) $this->moduleId));
        }

        return $deleted;
    }
}