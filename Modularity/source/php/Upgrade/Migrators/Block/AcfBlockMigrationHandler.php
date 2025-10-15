<?php

namespace Modularity\Upgrade\Migrators\Block;

use Modularity\Upgrade\Migrators\Block\AcfBlockFieldMigrator;
use Modularity\Upgrade\Migrators\Block\AcfBlockRepeaterFieldsMigrator;
use Modularity\Upgrade\Migrators\Block\AcfBlockReplaceAndUpdateSelectFieldMigrator;
use Modularity\Upgrade\Migrators\Block\AcfBlockRemoveFieldMigrator;

class AcfBlockMigrationHandler {

    private $fields;
    private $blockData;

    public function __construct($fields, $blockData) {

        $this->fields = $fields;
        $this->blockData = $blockData;
    }

    /**
     * Block: Extract a field value and adds it to another field.
     * 
     * @param array $fields Fields is an array with the old name of the field being a key and the value being the new name of the field
     * @param array $blockData All the data of the block (the acf fields attached to the block)
     */
    public function migrateBlockFields() 
    {        
        if ($this->isValidInputParams()) {
            return $this->blockData;
        }
        foreach ($this->fields as $oldFieldName => $newField) {
            if (isset($this->blockData[$oldFieldName]) && is_array($newField)) {
                $this->blockData = $this->migrateField($newField, $oldFieldName, $this->blockData);
            }
        }

        return $this->blockData;
    }

    private function migrateField($newField, $oldFieldName, $blockData) {
        if (!empty($newField['type'])) {
            return $this->migrateFieldByType($newField, $oldFieldName, $blockData);
        }

        if (empty($newField['name']) || empty($newField['key'])) {
            return $blockData;
        }

        $migrator = new AcfBlockFieldMigrator($newField, $oldFieldName, $blockData);
        return $migrator->migrate();
    }

    private function migrateFieldByType($newField, $oldFieldName, $blockData) {
        if ($this->isRemoveFieldMigration($newField)) {
            $migrator = new AcfBlockRemoveFieldMigrator($blockData, $oldFieldName);
        } 
        elseif ($this->isReplaceAndUpdateFieldMigration($newField)) {
            $migrator = new AcfBlockReplaceAndUpdateSelectFieldMigrator($newField, $oldFieldName, $blockData);
        } 
        elseif ($this->isRepeaterFieldMigration($newField)) {
            $migrator = new AcfBlockRepeaterFieldsMigrator($newField['name'], $newField['key'], $newField['fields'], $oldFieldName, $blockData);
        } 
        elseif ($this->isCustomFieldMigration($newField)) {
            $class = '\\Modularity\Upgrade\Migrators\Block\Custom\\' . $newField['class'];
            $migrator = new $class($newField, $oldFieldName, $blockData);
        }
        return 
            isset($migrator) ?
            $migrator->migrate() :
            $blockData;
    }

    private function isRemoveFieldMigration($newField) {
        return $newField['type'] == 'removeField';
    }

    private function isReplaceAndUpdateFieldMigration($newField) {
        return 
            $newField['type'] == 'replaceValue' && 
            isset($newField['values']) && 
            is_array($newField['values']) && 
            !empty($newField['name']) &&
            !empty($newField['key']);
    }
    
    private function isRepeaterFieldMigration($newField) {
        return 
            $newField['type'] == 'repeater' && 
            isset($newField['fields']) && 
            is_array($newField['fields']) &&
            !empty($newField['name']) &&
            !empty($newField['key']);
    }

    private function isCustomFieldMigration($newField) {
        return 
            $newField['type'] == 'custom' && 
            !empty($newField['class']) && 
            class_exists('\\Modularity\Upgrade\Migrators\Block\Custom\\' . $newField['class']);
    }

    private function isValidInputParams() {
        return 
            empty($this->fields) || 
            !is_array($this->fields) || 
            empty($this->blockData) || 
            !is_array($this->blockData);
    }

}