<?php 

namespace Municipio\Admin\Acf\ContentTypeSchema;

use \Municipio\Admin\Acf\ContentTypeMetaFieldManager as FieldManager;

use \Municipio\Helper\WP;
use \Municipio\Helper\ContentType;

class PrepareField {
    private $fieldManager;

    public function __construct(FieldManager $fieldManager) {
        $this->fieldManager = $fieldManager;
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
            $field['key'] === $this->fieldManager->getFieldKey()
            || str_contains($field['key'], "{$this->fieldManager->getGroupName()}_description")
            || !str_contains($field['id'], $this->fieldManager->getGroupName())
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
