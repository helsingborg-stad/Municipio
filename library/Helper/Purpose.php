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
    public static function getPurpose(string $postType = null) : string
    {
        return get_option('options_purpose_' . $postType);
    }
}
