<?php

namespace Modularity\Upgrade\Version;

use \Modularity\Upgrade\Version\Helper\GetPostsByPostType;
use \Modularity\Upgrade\Migrators\Module\AcfModuleRepeaterFieldsMigrator;

/**
 * Class V8
 * This version migrates really old posts modules that have previously been migrated to manualinput in the V5 upgrade.
 * Due to the old structure, this was not done properly and the data is not correctly migrated.
 * 
 * @package Modularity\Upgrade\Version
 */

class V8 implements versionInterface {
    private $db;
    private $oldKey = 'data';
    private $newKey = 'manual_inputs';

    public function __construct(\wpdb $db) {
      $this->db = $db;
    }

    public function upgrade(): bool
    {
      $this->upgradeModules();
      return true;
    }

    /**
     * Migrate modules from old key to new key
     * 
     * @return bool
     */
    private function upgradeModules() 
    {
      $modulesMatchingCriteria = $this->getModules(); 

      if (empty($modulesMatchingCriteria)) {
        return;
      }

      foreach ($modulesMatchingCriteria as $module) {
        $oldFieldValue = $this->getUndefinedField($module->ID, $this->oldKey);

        $newField = [
          'type' => 'repeater',
          'name' => $this->newKey,
          'fields' => [
            'post_title' => 'title',
            'post_content' => 'content',
            'permalink' => 'permalink',
            'permalink' => 'link',
            'image' => 'image',
            'column_values' => 'accordion_column_values',
          ]
        ];
        $migrator = new AcfModuleRepeaterFieldsMigrator($newField, $oldFieldValue, $module->ID);
        $migrator->migrate();
      }
    }

    /**
     * A version of get_field that can handle fields that are not defined in ACF
     * @supports: repeater fields
     * 
     * @param int $postId
     * @param string $fieldKey
     * 
     * @return array
     */
    private function getUndefinedField($postId, $fieldKey) {
      $query = $this->db->prepare(
        "SELECT * FROM {$this->db->postmeta} WHERE post_id = %d AND meta_key LIKE ",
        $postId 
      ) . "'" . $fieldKey . "_%_%'";

      $results = $this->db->get_results($query, ARRAY_A);

      // Group results by prefix
      $groupedData = [];
      foreach ($results as $row) {
          if (preg_match('/' . preg_quote($fieldKey, '/') . '_(\d+)_(.+)$/', $row['meta_key'], $matches)) {
              $index = (int)$matches[1]; // Extract the numeric index (e.g., 0, 1, etc.)
              $key = $matches[2];        // Extract the key (e.g., post_title, post_content, etc.)
              $groupedData[$index][$key] = $row['meta_value'];
          }
      }
      ksort($groupedData);

      return $groupedData;
    }

    /**
     * Get modules that should be migrated. We assume that all 
     * manualinput modules that does not have any data in the 
     * manual_inputs field should be migrated.
     * 
     * @return array
     */
    private function getModules(): array 
    {
        $postsModules = GetPostsByPostType::getPostsByPostType('mod-manualinput');
        $filteredPostsModules = array_filter($postsModules, function ($module) {
            if (!empty($module->ID)) {
                return empty(get_field($this->newKey, $module->ID) ?? false);
            }
            return false;
        });
        return $filteredPostsModules ?? [];
    }
}