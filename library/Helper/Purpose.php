<?php

namespace Municipio\Helper;

class Purpose
{
    public static function getPurposes() : array
    {
        // TODO: Set purposes based on what files exist in the templates/Purpose folder
        return [
            'project' => __('Project', 'municipio'),
        ];
    }
/**
 * `getPurpose()` returns the value of the `options_purpose_X` option, where X is the post
 * type name
 *
 * @param string|WP_Post_Type postType The post type you want to get the purpose for.
 *
 * @return string|bool The value of the option with the key 'options_purpose_X'. Returns false if option is missing.
 */
    public static function getPurpose($postType = null)
    {
        if(empty($postType)) {
            $postType = get_queried_object();
        }
        
        if (is_a($postType, 'WP_Post_Type')) {
            $postType = $postType->name;
        }
        elseif (is_a($postType, 'WP_Post')) {
            $postType = $postType->post_type;
        }
        
        $purpose = get_option('options_purpose_' . $postType);
        if ('' === $purpose) {
            return false;
        }
        
        return $purpose;
    }
}
