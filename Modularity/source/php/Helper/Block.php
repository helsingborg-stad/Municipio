<?php

namespace Modularity\Helper;

class Block
{
    public static function getBlockData($post, string $blockName, string $fieldName)
    {
        if (is_int($post)) {
            $post = get_post($post);
        }
        if (!is_a($post, 'WP_Post')) {
            return false;
        }

        $content = '';
        if (has_blocks($post->post_content)) {
            $blocks = parse_blocks($post->post_content);
            foreach ($blocks as $block) {
                if ($block['blockName'] === $blockName) {
                    if (isset($block["attrs"]["data"][$fieldName])) {
                        $content = $block["attrs"]["data"][$fieldName];
                    }
                }
            }
        }
        return $content;
    }
}
