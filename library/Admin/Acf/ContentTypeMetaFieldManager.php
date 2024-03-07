<?php

namespace Municipio\Admin\Acf;

use Municipio\Admin\Acf\ContentTypeSchema\RegisterFields;
use Municipio\Admin\Acf\ContentTypeSchema\PrepareField;
use Municipio\Admin\Acf\ContentTypeSchema\SavePost;

class ContentTypeMetaFieldManager
{
    protected $fieldGroupKey = 'group_schema';
    protected $fieldKey      = 'field_schema';
    protected $groupName     = 'schema';

    public function registerFields() {
        $registerFields = new RegisterFields($this);
        $registerFields->setup();
    }
    
    public function addHooks():void {
        
        $prepareField = new PrepareField($this);
        add_filter('acf/prepare_field', [$prepareField, 'maybeLoadField'], 10, 2);

        $savePost = new SavePost();
        add_action('acf/save_post', [$savePost, 'updatePostSchemaWithAddress'], 10, 1);
    }

    public function getFieldGroupKey() : string {
        return $this->fieldGroupKey;
    }
    
    public function getFieldKey() : string {
        return $this->fieldKey;
    }

    public function getGroupName() : string {
        return $this->groupName;
    }
}
