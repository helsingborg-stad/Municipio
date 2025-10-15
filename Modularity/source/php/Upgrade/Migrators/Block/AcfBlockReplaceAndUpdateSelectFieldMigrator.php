<?php

namespace Modularity\Upgrade\Migrators\Block;

use Modularity\Upgrade\Migrators\MigratorInterface;

class AcfBlockReplaceAndUpdateSelectFieldMigrator implements MigratorInterface {

    private $newField;
    private $oldFieldName;
    private $blockData;

    public function __construct($newField, $oldFieldName, $blockData) {

        $this->newField = $newField;
        $this->oldFieldName = $oldFieldName;
        $this->blockData = $blockData;
    }

    public function migrate():mixed {

        if (!$this->isValidInputParams()) {
            return $this->blockData;
        }

        $this->blockData['_' . $this->newField['name']] = $this->newField['key'];
        $this->blockData[$this->newField['name']] = $this->getNewValues();

        return $this->blockData;
    }

    private function getNewValues() {
        if (isset($this->newField['values'][$this->blockData[$this->oldFieldName]])) {
            return $this->newField['values'][$this->blockData[$this->oldFieldName]];
        }

        return $this->newField['values']['default'];
    }

    private function isValidInputParams() {
        return 
            is_array($this->newField) &&
            is_string($this->oldFieldName) &&
            is_array($this->blockData) &&
            !empty($this->newField) &&
            !empty($this->oldFieldName) &&
            !empty($this->blockData) && 
            is_string($this->newField['name']) &&
            !empty($this->newField['values']['default']);
    }
}