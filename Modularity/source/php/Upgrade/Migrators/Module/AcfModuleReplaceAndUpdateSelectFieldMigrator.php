<?php

namespace Modularity\Upgrade\Migrators\Module;

use Modularity\Upgrade\Migrators\MigratorInterface;
use WP_CLI;

class AcfModuleReplaceAndUpdateSelectFieldMigrator implements MigratorInterface {

    private $newField;
    private $oldFieldValue;
    private $moduleId;

    public function __construct($newField, $oldFieldValue, $moduleId) {
        $this->newField         = $newField;
        $this->oldFieldValue    = $oldFieldValue;
        $this->moduleId         = $moduleId;
    }

    public function migrate():mixed {
        if (!empty($this->newField['values'][$this->oldFieldValue])) { 
            $valueReplaced = update_field($this->newField['name'], $this->newField['values'][$this->oldFieldValue], $this->moduleId);

            if($valueReplaced) {
                WP_CLI::line(sprintf('Updating field %s with value %s in %s', (string) $this->newField['name'], (string) $this->newField['values'][$this->oldFieldValue], (string) $this->moduleId));
            } else {
                WP_CLI::warning(sprintf('Failed to update field %s with value %s in %s', (string) $this->newField['name'], (string) $this->newField['values'][$this->oldFieldValue], (string) $this->moduleId));
            }
            return $valueReplaced;
        } 

        if(!empty($this->newField['values']['default'])) {
            $valueDefaulted = update_field($this->newField['name'], $this->newField['values']['default'], $this->moduleId);

            if($valueDefaulted) {
                WP_CLI::line(sprintf('Updating field %s with value %s in %s', (string) $this->newField['name'], (string) $this->newField['values']['default'], (string) $this->moduleId));
            } else {
                WP_CLI::warning(sprintf('Failed to update field %s with value %s in %s', (string) $this->newField['name'], (string) $this->newField['values']['default'], (string) $this->moduleId));
            }

            return $valueDefaulted;
        }

        return false;
    }
}