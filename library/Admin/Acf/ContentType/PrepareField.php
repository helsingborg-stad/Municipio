<?php

namespace Municipio\Admin\Acf\ContentType;

use \Municipio\Helper\WP;
use \Municipio\Helper\ContentType;

/**
 * Functionality to run on prepare_field filter
 */
class PrepareField {
    private string $fieldKey;
    private string $groupName;

    /**
     * Constructor
     */
    public function __construct(string $fieldKey, string $groupName) {
        $this->fieldKey = $fieldKey;
        $this->groupName = $groupName;
    }

    public function addHooks():void {
        add_filter('acf/prepare_field', [$this, 'maybeLoadField'], 10, 2);
    }

    /**
     * Checks if a field should be loaded based on the current post type and field properties.
     *
     * @param array $field The field to check.
     * @return mixed The field if it should be loaded, false otherwise.
     */
    public function maybeLoadField($field)
    {
        $postType = WP::getCurrentPostType();

        if (
            $field['key'] === $this->fieldKey
            || str_contains($field['key'], "{$this->groupName}_description")
            || !str_contains($field['id'], $this->groupName)
            || empty($postType)
        ) {
            return $field;
        }

        $postContentType = ContentType::getContentType($postType);

        if (!str_contains($field['id'], $postContentType->getKey())) {
            return false;
        }

        return $field;
    }
}
