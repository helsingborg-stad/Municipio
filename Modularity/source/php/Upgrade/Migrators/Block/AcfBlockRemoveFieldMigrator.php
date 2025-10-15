<?php

namespace Modularity\Upgrade\Migrators\Block;

use Modularity\Upgrade\Migrators\MigratorInterface;

class AcfBlockRemoveFieldMigrator implements MigratorInterface {

    private $fieldName;
    private $blockData;

    public function __construct($fieldName, $blockData) {

        $this->fieldName = $fieldName;
        $this->blockData = $blockData;
    }

    public function migrate():mixed {
        if (!$this->isValidInputParams()) {
            return $this->blockData;
        }

        unset($this->blockData[$this->fieldName]);
        unset($this->blockData['_' . $this->fieldName]);

        return $this->blockData;
    }

    private function isValidInputParams() {
        return 
            is_string($this->fieldName) &&
            is_array($this->blockData) &&
            !empty($this->fieldName) &&
            !empty($this->blockData);
    }
}