<?php

namespace Modularity\Upgrade\Migrators\Module\Custom;

use Modularity\Upgrade\Migrators\MigratorInterface;

class AcfModuleIndexRepeaterMigrator implements MigratorInterface {

    private $newField;
    private $oldFieldValue;
    private $moduleId;

    public function __construct($newField, $oldFieldValue = [], $moduleId) {
        $this->newField = $newField;
        $this->oldFieldValue = $oldFieldValue;
        $this->moduleId = $moduleId;
    }

    public function migrate():mixed {
        update_field('display_as', 'card', $this->moduleId);
        
        $updateValue = [];
            
        if (!empty($this->oldFieldValue) && is_array($this->oldFieldValue)) {
            $updateValue = [];
        
            foreach ($this->oldFieldValue as $oldInput) {
                $val = [
                    'image_before_content' => false,
                    'content' => !empty($oldInput['lead']) ? $oldInput['lead'] : (!empty($oldInput['page']->post_content) ? $this->getIndexExcerpt($oldInput['page']->post_content) : false),
                    'title' => !empty($oldInput['title']) ? $oldInput['title'] : (!empty($oldInput['page']->post_title) ? $oldInput['page']->post_title : false),
                    'image' => !empty($oldInput['custom_image']['ID']) ? $oldInput['custom_image']['ID'] : false,
                    'link' => !empty($oldInput['link_url']) ? $oldInput['link_url'] : false,
                ];
                
                if (!empty($oldInput['link_type'])) {
                    if ($oldInput['link_type'] == 'internal' && !empty($oldInput['page']->ID)) {
                        $val['link'] = get_permalink($oldInput['page']->ID);
                        if (!empty($oldInput['image_display']) && $oldInput['image_display'] == 'featured') {
                            $val['image'] = get_post_thumbnail_id($oldInput['page']->ID);
                        }   
                    }
                    
                    if ($oldInput['link_type'] == 'unlinked') {
                        $val['link'] = false;
                    }
                }
                
                $updateValue[] = $val;
            }

            return update_field($this->newField['name'], $updateValue, $this->moduleId);
        }
        
        return false;
    }

    /* TODO: Remove after upgrade */
    private function getIndexExcerpt($postContent) {
        $postContent = preg_replace('#</?a(\s[^>]*)?>#i', '', $postContent);
        if (strpos($postContent, "<!--more-->")) {
            return strip_tags(substr($postContent, 0, strpos($postContent, "<!--more-->")));
        }

        $postContent = wp_trim_words(strip_tags($postContent), 55, '...');

        return $postContent;
    }
}