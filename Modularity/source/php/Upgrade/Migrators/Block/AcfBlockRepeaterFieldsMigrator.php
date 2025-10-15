<?php

namespace Modularity\Upgrade\Migrators\Block;

use Modularity\Upgrade\Migrators\MigratorInterface;

class AcfBlockRepeaterFieldsMigrator implements MigratorInterface {

    private $newFieldName;
    private $newFieldKey;
    private $newFieldFields;
    private $oldFieldName;
    private $blockData;

    public function __construct($newFieldName, $newFieldKey, $newFieldFields, $oldFieldName, $blockData) {
        $this->newFieldName = $newFieldName;
        $this->newFieldKey = $newFieldKey;
        $this->newFieldFields = $newFieldFields;
        $this->oldFieldName = $oldFieldName;
        $this->blockData = $blockData;
    }

    public function migrate():mixed {
        if (!$this->isValidInputParams()) {
            return $this->blockData;
        }

        $this->blockData[$this->newFieldName] = $this->blockData[$this->oldFieldName];
        $this->blockData['_' . $this->newFieldName] = $this->newFieldKey;

        $this->migrateBlockRepeater();

        return $this->blockData;
    }


    private function migrateBlockRepeater() {

        foreach ($this->newFieldFields as $oldRepeaterFieldName => $newRepeaterFieldName) {

            $i = 0;

            while (isset($this->blockData[$this->oldFieldName . '_' . $i . '_' . $oldRepeaterFieldName])) {
                $newName = $this->newFieldName . '_' . $i . '_' . $newRepeaterFieldName['name'];
                $oldName = $this->oldFieldName . '_' . $i . '_' . $oldRepeaterFieldName;
                $this->blockData[$newName] = $this->blockData[$oldName];
                $this->blockData['_' . $newName] = $newRepeaterFieldName['key'];
                if ($this->isNestedRepeaterField($newRepeaterFieldName)) {
                    $migrator = new self($newName, $newRepeaterFieldName['key'], $newRepeaterFieldName['fields'], $oldName, $this->blockData);
                    $this->blockData = $migrator->migrateBlockRepeater();
                }
                // unset($blockData[$oldFieldName . '_' . $i . '_' . $oldRepeaterFieldName]);
                $i++;
            }

        }
        return $this->blockData;
    }

    private function isValidInputParams(): bool {
        return
            is_string($this->newFieldName) &&
            is_string($this->oldFieldName) &&
            is_string($this->newFieldKey) &&
            !empty($this->newFieldName) &&
            !empty($this->oldFieldName) &&
            !empty($this->newFieldFields) &&
            is_array($this->newFieldFields) && 
            is_array($this->blockData);
    }

    private function isNestedRepeaterField(array $newRepeaterFieldName):bool {
        return 
            !empty($newRepeaterFieldName['type']) && 
            $newRepeaterFieldName['type'] === 'repeater' && 
            !empty($newRepeaterFieldName['fields']) && 
            is_array($newRepeaterFieldName['fields']) &&
            !empty($newRepeaterFieldName['key']) && 
            is_string($newRepeaterFieldName['key']);
    }
}