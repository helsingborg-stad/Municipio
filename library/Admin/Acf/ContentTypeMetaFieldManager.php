<?php

namespace Municipio\Admin\Acf;

use Municipio\Admin\Acf\ContentTypeSchema\RegisterFields;
use Municipio\Admin\Acf\ContentTypeSchema\PrepareField;
use Municipio\Admin\Acf\ContentTypeSchema\SavePost;

/**
 * Manages the registration and configuration of custom ACF field groups for content types.
 */

class ContentTypeMetaFieldManager
{
    protected $fieldGroupKey = 'group_schema';
    protected $fieldKey      = 'field_schema';
    protected $groupName     = 'schema';

    /**
     * Initializes the custom ACF field groups for content types.
     */
    public function registerFields() {
        $registerFields = new RegisterFields($this);
        $registerFields->setup();
    }

    /**
     * Run hooks for the custom ACF field groups for content types.
     */
    public function addHooks():void {

        $prepareField = new PrepareField($this);
        add_filter('acf/prepare_field', [$prepareField, 'maybeLoadField'], 10, 2);

        $savePost = new SavePost();
        add_action('acf/save_post', [$savePost, 'updatePostSchemaWithAddress'], 10, 1);
    }

    /**
     * Returns the field group key
     */
    public function getFieldGroupKey() : string {
        return $this->fieldGroupKey;
    }

    /**
     * Returns the field key
     */
    public function getFieldKey() : string {
        return $this->fieldKey;
    }

    /**
     * Returns the field group name
     */
    public function getGroupName() : string {
        return $this->groupName;
    }
}
