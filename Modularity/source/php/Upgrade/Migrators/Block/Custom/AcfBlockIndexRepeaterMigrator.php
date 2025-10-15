<?php

namespace Modularity\Upgrade\Migrators\Block\Custom;

use Modularity\Upgrade\Migrators\MigratorInterface;

class AcfBlockIndexRepeaterMigrator implements MigratorInterface {

    private $newField;
    private $oldFieldName;
    private $blockData;

    public function __construct($newField, $oldFieldName, $blockData) {
        $this->newField = $newField;
        $this->oldFieldName = $oldFieldName;
        $this->blockData = $blockData;
    }

    public function migrate():mixed {
        $newFieldName = $this->newField['name'];
        $newFieldKey = $this->newField['key'];
        $this->blockData[$newFieldName] = $this->blockData[$this->oldFieldName];
        $this->blockData['_' . $newFieldName] = $newFieldKey;
        if (is_array($this->blockData)) {
            $indexedArrays = [];
        
            foreach ($this->blockData as $key => $value) {
                if (preg_match('/^index_(\d+)_(.*)/', $key, $matches)) {
                    if (isset($matches[1]) && isset($matches[2])) {
                        $index = $matches[1];
                        $indexedArrays[$index][$matches[2]] = $value;
                    }
                }
            }

            if (!empty($indexedArrays) && is_array($indexedArrays)) {
                foreach ($indexedArrays as $index => $values) {
                    if (!empty($values['link_type'])) {
                        $title = !empty($values['title']) ? $values['title'] : ($values['link_type'] == 'internal' && !empty($values['page']) ? get_the_title($values['page']) : false);
    
                        $content = !empty($values['lead']) ? $values['lead'] : ($values['link_type'] == 'internal' && !empty($values['page']) ? $this->getIndexExcerpt(get_the_content(null, true, $values['page'])) : false);
                        
                        $image = $values['link_type'] == 'internal' && !empty($values['page']) && !empty($values['image_display']) && $values['image_display'] == 'featured' ? get_post_thumbnail_id($values['page']) : (!empty($values['custom_image']) ? $values['custom_image'] : false);

                        $link = $values['link_type'] == 'internal' && !empty($values['page']) ? get_permalink($values['page']) : (!empty($values['link_url']) && $values['link_type'] == 'external' ? $values['link_url'] : false);
                        
                        $this->blockData[$newFieldName . '_' . $index . '_title'] = $title;
                        $this->blockData['_' . $newFieldName . '_' . $index . '_title'] = 'field_64ff22fdd91b8';

                        $this->blockData[$newFieldName . '_' . $index . '_content'] = $content;
                        $this->blockData['_' . $newFieldName . '_' . $index . '_content'] = 'field_64ff231ed91b9';

                        $this->blockData[$newFieldName . '_' . $index . '_image'] = $image;
                        $this->blockData['_' . $newFieldName . '_' . $index . '_image'] = 'field_64ff2355d91bb';

                        $this->blockData[$newFieldName . '_' . $index . '_link'] = $link;
                        $this->blockData['_' . $newFieldName . '_' . $index . '_link'] = 'field_64ff232ad91ba';  
                    }
                }
            }
            
            $this->blockData['display_as'] = 'card';
            $this->blockData['_display_as'] = 'field_64ff23d0d91bf';
        }

        return $this->blockData;
    }

    private function getIndexExcerpt($postContent) {
        $postContent = preg_replace('#</?a(\s[^>]*)?>#i', '', $postContent);
        if (strpos($postContent, "<!--more-->")) {
            return strip_tags(substr($postContent, 0, strpos($postContent, "<!--more-->")));
        }

        $postContent = wp_trim_words(strip_tags($postContent), 55, '...');

        return $postContent;
    }
}