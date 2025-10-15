<?php

namespace Modularity\Upgrade\Migrators\Block;

use Modularity\Upgrade\Migrators\MigratorInterface;

class AcfBlockFieldMigrator implements MigratorInterface {

    private $newField;
    private $oldFieldName;
    private $blockData;

    public function __construct($newField, $oldFieldName, $blockData) {
        $this->newField = $newField;
        $this->oldFieldName = $oldFieldName;
        $this->blockData = $blockData;
    }

    public function migrate():mixed {
        $this->blockData[$this->newField['name']] = $this->blockData[$this->oldFieldName];
        $this->blockData['_' . $this->newField['name']] = $this->newField['key'];

        return $this->blockData;
    }
}