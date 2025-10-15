<?php

namespace Modularity\Upgrade\Migrators\Module;


class AcfModuleMigrationHandler {

    private $fields;
    private $moduleId;

    public function __construct(array $fields, int|bool $moduleId) {

        $this->fields = $fields;
        $this->moduleId = $moduleId;
    }

    public function migrateModuleFields():array 
    {
        $fieldsWasUpdated = [];
        foreach ($this->fields as $oldFieldName => $newField) {
            if (is_array($newField) || is_string($newField)) {
                $fieldsWasUpdated[] = $this->migrateField($oldFieldName, $newField);
            } 
        }

        return $fieldsWasUpdated;
    }

    private function migrateField(string $oldFieldName, $newField):bool 
    {
        $oldFieldValue = get_field($oldFieldName, $this->moduleId);
        if (!empty($newField['type'])) {
            return $this->migrateFieldByType($oldFieldName, $oldFieldValue, $newField);
        }

        if (is_string($newField)) {
            $migrator = new AcfModuleFieldMigrator($newField, $oldFieldValue, $this->moduleId);
            return $migrator->migrate();
        }
    }

    private function migrateFieldByType(string $oldFieldName, $oldFieldValue, array $newField):bool 
    {
        if ($this->isRemoveFieldMigration($newField)) {
            $migrator = new AcfModuleRemoveFieldMigrator($oldFieldName, $this->moduleId);
        } 
        elseif ($this->isReplaceAndUpdateFieldMigration($newField)) {
            $migrator = new AcfModuleReplaceAndUpdateSelectFieldMigrator($newField, $oldFieldValue, $this->moduleId);
        } 
        elseif ($this->isRepeaterFieldMigration($newField)) {
            $migrator = new AcfModuleRepeaterFieldsMigrator($newField, $oldFieldValue, $this->moduleId);
        } 
        elseif ($this->isCustomFieldMigration($newField)) {
            $class = '\\Modularity\Upgrade\Migrators\Module\Custom\\' . $newField['class'];
            $migrator = new $class($newField, $oldFieldValue, $this->moduleId);
        }

        return isset($migrator) ? $migrator->migrate() : false;
    }

    private function isRemoveFieldMigration($newField) {
        return 
            $newField['type'] == 'removeField';
    }

    private function isReplaceAndUpdateFieldMigration(array $newField):bool {
        return 
            $newField['type'] == 'replaceValue' && 
            isset($newField['values']) && 
            is_array($newField['values']) &&
            !empty($newField['name']) &&
            is_string($newField['name']);
    }
    
    private function isRepeaterFieldMigration(array $newField):bool {
        return 
            $newField['type'] == 'repeater' && 
            isset($newField['fields']) && 
            is_array($newField['fields']) && 
            !empty($newField['name']) && 
            is_string($newField['name']);
        }

    private function isCustomFieldMigration(array $newField):bool {
        return 
            $newField['type'] == 'custom' && 
            !empty($newField['class']) && 
            class_exists('\\Modularity\Upgrade\Migrators\Module\Custom\\' . $newField['class']);
    }
}