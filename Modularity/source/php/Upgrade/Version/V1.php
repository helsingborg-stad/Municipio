<?php

namespace Modularity\Upgrade\Version;

use \Modularity\Upgrade\Migrators\Block\AcfBlockMigration;
use \Modularity\Upgrade\Migrators\Module\AcfModuleMigration;
use \Modularity\Upgrade\Version\Helper\GetPostsByPostType;

class V1 implements versionInterface {
    private $db;
    private $name;

    public function __construct(\wpdb $db) {
        $this->db = $db;
        $this->name = 'divider';
    }

    public function upgrade(): bool
    {
        $this->upgradeBlocks();
        $this->upgradeModules();

        return true;
    }

    private function upgradeModules()
    {     
        $dividers = GetPostsByPostType::getPostsByPostType('mod-' . $this->name);

        if (!empty($dividers)) {
            foreach ($dividers as &$divider) {
                $dividerTitleField = get_field('divider_title', $divider->ID);

                if (!empty($dividerTitleField) && is_string($dividerTitleField)) {
                    update_post_meta($divider->ID, 'modularity-module-hide-title', false);
                    wp_update_post([
                        'ID' => $divider->ID,
                        'post_title' => $dividerTitleField
                    ]);
                }
            }
        }
    }

    private function upgradeBlocks() 
    {
        $blockMigrator = new AcfBlockMigration(
            $this->db,
            'acf/' . $this->name,
            $this->getBlockFields()
        );

        $blockMigrator->migrateBlocks();
    }

    private function getBlockFields() 
    {
        return [
            'divider_title' => [
                'name' => 'custom_block_title', 
                'key' => 'field_block_title'
            ]
        ];
    }
}