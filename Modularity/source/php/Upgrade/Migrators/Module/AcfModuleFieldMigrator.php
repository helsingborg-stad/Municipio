<?php

namespace Modularity\Upgrade\Migrators\Module;

use Modularity\Upgrade\Migrators\MigratorInterface;
use WP_CLI;

class AcfModuleFieldMigrator implements MigratorInterface {

    private $newField;
    private $oldFieldValue;
    private $moduleId;

    public function __construct(string $newField, $oldFieldValue, int $moduleId) {
        $this->newField = $newField;
        $this->oldFieldValue = $oldFieldValue;
        $this->moduleId = $moduleId;
    }

    public function migrate():mixed {

        if (is_null($this->oldFieldValue)) {
            WP_CLI::line(sprintf('No actions needed on field %s, old value is null on module id %s.', (string) $this->newField, (string) $this->moduleId));
            return false;
        }

        $updated = update_field($this->newField, $this->oldFieldValue, $this->moduleId);

        if($updated) {
            WP_CLI::line(sprintf('Updating field %s with value %s in %s', (string) $this->newField, (string) $this->oldFieldValue, (string) $this->moduleId));
        } else {
            WP_CLI::warning(sprintf('Failed to update field %s with value %s in %s', (string) $this->newField, (string) $this->oldFieldValue, (string) $this->moduleId));
        }

        return $updated;
    }
}