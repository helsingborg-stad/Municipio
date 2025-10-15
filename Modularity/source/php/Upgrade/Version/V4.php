<?php

namespace Modularity\Upgrade\Version;

use \Modularity\Upgrade\Migrators\Block\AcfBlockMigration;
use \Modularity\Upgrade\Migrators\Module\AcfModuleMigration;
use \Modularity\Upgrade\Version\Helper\GetPostsByPostType;

class V4 implements versionInterface {
    private $db;
    private $name;

    public function __construct(\wpdb $db) {
        $this->db = $db;
        $this->name = 'manualinput';
    }

    public function upgrade(): bool
    {
        $this->upgradeBlocks();
        $this->upgradeModules();

        return true;
    }

    private function upgradeModules() 
    {
        $moduleMigrator = new AcfModuleMigration(
            $this->db,
            GetPostsByPostType::getPostsByPostType('mod-' . $this->name),
            $this->getFields()
        );

        $moduleMigrator->migrateModules();
    }

    private function upgradeBlocks() 
    {
        $blockMigrator = new AcfBlockMigration(
            $this->db,
            'acf/' . $this->name,
            $this->getFields()
        );

        $blockMigrator->migrateBlocks();
    }

    private function getFields() 
    {
        return 
        [
            'index' => [
                'type' => 'removeField',
            ],
            'index_columns' => [
                'type' => 'removeField',
            ]
        ];
    }
}