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
            $post_id = $screen['post_id'];
            $post = get_post($post_id);
            if (!$post) {
                return $rule;
            } else {
                $type = $post->post_type;
            }
        } else {
            return false;
        }
        // Compare the post attribute to rule value.
        $purpose = \Municipio\Helper\Purpose::getPurpose($type);
        if ('' === $purpose) {
            return false;
        }

        $result = ($purpose === $rule['value']);

        // Return result taking into account the operator type.
        if ($rule['operator'] == '!=') {
            return !$result;
        }
        return $result;
    }
}
