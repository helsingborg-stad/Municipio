<?php

class LocationRulesPurpose extends \ACF_Location // @codingStandardsIgnoreLine
{
    public function initialize()
    {
        $this->name = 'purpose';
        $this->label = __("Purpose", 'municipio');
        $this->category = 'post';
        $this->object_type = 'post';
    }

    public function get_values($rule) // @codingStandardsIgnoreLine
    {
        return \Municipio\Helper\Purpose::getRegisteredPurposes(false);
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
        $purposes = \Municipio\Helper\Purpose::getPurpose($type, true);
        if (!empty($purposes)) {
            foreach ($purposes as $purpose) {
                $result = ($purpose->key === $rule['value']);
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
