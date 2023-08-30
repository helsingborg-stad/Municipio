<?php

class LocationRulesContentType extends \ACF_Location // @codingStandardsIgnoreLine
{
    public function initialize()
    {
        $this->name = 'content_type';
        $this->label = __("Content type", 'municipio');
        $this->category = 'post';
        $this->object_type = 'post';
    }

    public function get_values($rule) // @codingStandardsIgnoreLine
    {
        return \Municipio\Helper\ContentType::getRegisteredContentTypes(false);
    }

    public function match($rule, $screen, $field_group)
    {
        if (!empty($screen['taxonomy_list'])) {
            $type = $screen['taxonomy_list'];
        } elseif (!empty($screen['post_id'])) {
            $postId = $screen['post_id'];
            $post = get_post($postId);
            if (!$post) {
                return $rule;
            } else {
                $type = $post->post_type;
            }
        } else {
            return false;
        }
        // Compare the post attribute to rule value.
        $contentTypes = \Municipio\Helper\ContentType::getContentType($type, true);
        if (!empty($contentTypes)) {
            foreach ($contentTypes as $contentType) {
                $result = ($contentType->key === $rule['value']);
                $returnResult = $result;
                // Return result taking into account the operator type.
                if ($rule['operator'] == '!=') {
                    $returnResult = !$result;
                }
            }
        } else {
            $returnResult = false;
        }

        return $returnResult;
    }
}
