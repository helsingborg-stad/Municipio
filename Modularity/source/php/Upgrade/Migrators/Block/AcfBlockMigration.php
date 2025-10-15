<?php

namespace Modularity\Upgrade\Migrators\Block;

use Modularity\Upgrade\Migrators\Block\AcfBlockMigrationHandler;
use \Modularity\Upgrade\Version\Helper\GetPagesByBlockName;
use Modularity\Upgrade\Version\Helper\UpdatePageContent;
use WP_CLI;

class AcfBlockMigration {

    public function __construct(
        private \wpdb $db, 
        private string $blockName, 
        private array $fields = [], 
        private $newBlockName = false, 
        private $conditionCallback = false,
        private $pages = null
    ) {
        $this->pages = $this->pages ?? GetPagesByBlockName::getPagesByBlockName($this->db, $this->blockName);
    }

    public function migrateBlocks() 
    {   
        if ($this->isValidPagesAndFields()) {
            foreach ($this->pages as $page) {
                $blocks = $this->updateBlocks(parse_blocks($page->post_content));

                $this->updatePageContent($blocks, $page);
                WP_CLI::line('Blocks migrated for page: ' . $page->post_title);
            }
        } else {
            WP_CLI::warning('No pages or fields found for block migration.');
        }
    }

    private function updateBlocks($blocks):array {
        if (empty($blocks) || !is_array($blocks)) {
            return [];
        }
        foreach ($blocks as &$block) {
            if (!empty($block['blockName']) && $block['blockName'] === $this->blockName && !empty($block['attrs']['data']) && $this->blockCondition($block)) {
                $migrationFieldManager = new AcfBlockMigrationHandler($this->fields, $block['attrs']['data']);
                $block['attrs']['data'] = $migrationFieldManager->migrateBlockFields();
                if (!empty($this->newBlockName)) {
                    $block['blockName'] = $this->newBlockName;
                    $block['attrs']['name'] = $this->newBlockName;
                }
            }

            if (!empty($block['innerBlocks'])) {
                $block['innerBlocks'] = $this->updateBlocks($block['innerBlocks']);
            }
        }

        return $blocks;
    }

    private function updatePageContent($blocks, $page) {
        $serializedBlocks = serialize_blocks($blocks); 

        UpdatePageContent::update($this->db, $page, $serializedBlocks);
    }

    private function isValidPagesAndFields():bool {
        return 
            !empty($this->pages) && 
            is_array($this->pages) && 
            !empty($this->fields) && 
            is_array($this->fields);
    }

    /**
     * Check a condition for a block based on a function.
     * 
     * @param string|false $function The name of the condition-checking function.
     * @param array $block The block data to be checked.
     * @return bool Returns true or the condition function.
     */
    private function blockCondition($block) {
        if (is_callable($this->conditionCallback)) {
            return call_user_func($this->conditionCallback, $block);
        }

        return true;
    }
}