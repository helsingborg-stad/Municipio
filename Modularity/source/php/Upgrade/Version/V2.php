<?php

namespace Modularity\Upgrade\Version;

use \Modularity\Upgrade\Migrators\Block\AcfBlockMigration;
use \Modularity\Upgrade\Migrators\Module\AcfModuleMigration;
use \Modularity\Upgrade\Version\Helper\GetPostsByPostType;

class V2 implements versionInterface {
    private $db;
    private $oldName;
    private $newName;

    public function __construct(\wpdb $db) {
        $this->db = $db;
        $this->oldName = 'index';
        $this->newName = 'manualinput';
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
            GetPostsByPostType::getPostsByPostType('mod-' . $this->oldName),
            $this->getModuleFields(),
            'mod-' . $this->newName
        );

        $moduleMigrator->migrateModules();
    }

    private function upgradeBlocks() 
    {
        $blockMigrator = new AcfBlockMigration(
            $this->db,
            'acf/' . $this->oldName,
            $this->getBlockFields(),
            'acf/' . $this->newName
        );

        $blockMigrator->migrateBlocks();
    }

    private function getModuleFields() 
    {
        return [
            'index' => [
                'name' => 'manual_inputs', 
                'type' => 'custom', 
                'class' => 'AcfModuleIndexRepeaterMigrator',
            ],
            'index_columns' => [
                'name' => 'columns',
                'type' => 'replaceValue',
                'values' => [
                    'grid-md-12' => 'o-grid-12',
                    'grid-md-6' => 'o-grid-6',
                    'grid-md-4' => 'o-grid-4',
                    'grid-md-3' => 'o-grid-3',
                    'default' => 'o-grid-4'
                ]
            ],
        ];
    }

    private function getBlockFields() 
    {
        return [
            'index_columns' => [
                'name' => 'columns',
                'key' =>'field_65001d039d4c4',
                'type' => 'replaceValue',
                'values' => [
                    'grid-md-12' => 'o-grid-12',
                    'grid-md-6' => 'o-grid-6',
                    'grid-md-4' => 'o-grid-4',
                    'grid-md-3' => 'o-grid-3',
                    'default' => 'o-grid-4'
                ]
            ], 
            'index' => [
                'type' => 'custom',
                'class' => 'AcfBlockIndexRepeaterMigrator',
                'name' => 'manual_inputs', 
                'key' => 'field_64ff22b2d91b7' 
            ]
        ];
    }
}