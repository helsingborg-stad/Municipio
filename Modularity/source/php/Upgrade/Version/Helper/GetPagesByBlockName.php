<?php

namespace Modularity\Upgrade\Version\Helper;

class GetPagesByBlockName {
    public static function getPagesByBlockName(\wpdb $db, string $blockName) {
        $pages = $db->get_results(
            "SELECT *
            FROM $db->posts
            WHERE post_content LIKE '%{$blockName}%'
            AND post_type != 'customize_changeset'"
        );
        
        return $pages;
    }
}