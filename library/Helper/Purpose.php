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
 * type
 * 
 * @param string postType The post type you want to get the purpose for.
 * 
 * @return string|bool The value of the option with the key 'options_purpose_X'. Returns false if option is missing. 
 */
    public static function getPurpose(string $postType = null)
    {
        $purpose = get_option('options_purpose_' . $postType);
        if('' === $purpose) {
            return false;
        }
        return $purpose;
    }
}
