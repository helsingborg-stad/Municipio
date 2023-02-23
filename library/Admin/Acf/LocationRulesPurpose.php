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
        return \Municipio\Helper\Purpose::getRegisteredPurposes();
    }

    public function match($rule, $screen, $field_group)
    {
        if (isset($screen['taxonomy_list'])) {
            $type = $screen['taxonomy_list'];
        } elseif (isset($screen['post_id'])) {
            $post_id = $screen['post_id'];
            $post = get_post($post_id);
            if (!$post) {
                return $rule;
            }
            $type = $post->post_type;
        } else {
            return false;
        }

        // Compare the post attribute to rule value.
        $purposes = \Municipio\Helper\Purpose::getPurpose($type);
        $purpose = $purposes['main'] ?? null;

        if (!$purpose) {
            return false;
        }

        $result = ($purpose === $rule['value']);

        // Return result taking into account the operator type.
        if ($rule['operator'] === '!=') {
            return !$result;
        }
        return $result;
    }
}
