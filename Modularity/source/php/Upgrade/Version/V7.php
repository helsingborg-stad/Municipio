<?php

namespace Modularity\Upgrade\Version;

use \Modularity\Upgrade\Migrators\Block\AcfBlockMigration;
use Modularity\Upgrade\Version\Helper\GetPagesByBlockName;
use \Modularity\Upgrade\Version\Helper\GetPostsByPostType;
use Modularity\Upgrade\Version\Helper\UpdatePageContent;
use WP_CLI;

class V7 implements versionInterface {
    private string $name      = 'posts';
    private string $fieldName = 'posts_display_as';
    private string $newValue  = 'index';
    private string $oldValue  = 'news';

    public function __construct(private \wpdb $db) 
    {
    }

    public function upgrade(): bool
    {
        $this->upgradeModules();
        $this->upgradeBlocks();

        return true;
    }

    private function upgradeModules() 
    {
        $postModules = GetPostsByPostType::getPostsByPostType('mod-' . $this->name);
        
        foreach ($postModules as $module) {
            if (empty($module->ID)) {
                continue;
            }

            $displayAs = get_field($this->fieldName, $module->ID);

            if (empty($displayAs) || $displayAs !== $this->oldValue) {
                continue;
            }

            $result = update_field($this->fieldName, $this->newValue, $module->ID);

            if ($result) {
                WP_CLI::line(sprintf('%s was updated', (string) $module->ID));
            } else {
                WP_CLI::warning(sprintf('Failed to update %s', (string) $module->ID));
            }
        }
    }

    private function upgradeBlocks() 
    {
        $pages = GetPagesByBlockName::getPagesByBlockName($this->db, 'acf/' . $this->name);

        if (empty($pages)) {
            WP_CLI::line('Success, no blocks to upgrade.');
        }

        foreach ($pages as $page) {
            $blocks = parse_blocks($page->post_content);
            $hasUpdatedBlocks = false;
            foreach ($blocks as &$block) {
                if ($block['blockName'] !== 'acf/' . $this->name) {
                    continue;
                }

                if (
                    empty($block['attrs']['data'][$this->fieldName]) || 
                    $block['attrs']['data'][$this->fieldName] !== $this->oldValue
                ) {
                    continue;
                }

                $block['attrs']['data'][$this->fieldName] = $this->newValue;
                $hasUpdatedBlocks = true;
            }

            if ($hasUpdatedBlocks) {
                $serializedBlocks = serialize_blocks($blocks);
                UpdatePageContent::update($this->db, $page, $serializedBlocks);
                WP_CLI::line('Updated blocks for page: ' . $page->ID);
            }
        }
    }
}