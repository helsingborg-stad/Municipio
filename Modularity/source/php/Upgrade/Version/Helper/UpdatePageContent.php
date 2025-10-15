<?php

namespace Modularity\Upgrade\Version\Helper;

class UpdatePageContent 
{
    public static function update($db, $page, $content)
    {
        if (!empty($content) && !empty($page->ID)) {
            $queryUpdateContent = $db->prepare(
                "UPDATE " . $db->posts . " SET post_content = %s WHERE ID = %d", 
                $content, 
                $page->ID
            ); 
            $db->query($queryUpdateContent); 
        }
    }
}