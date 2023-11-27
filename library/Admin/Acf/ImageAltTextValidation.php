<?php

namespace Municipio\Admin\Acf;

class ImageAltTextValidation
{
    public function __construct()
    {
        add_action( 'acf/validate_value', array($this, 'checkAttachedImagesAltTexts'), 10, 4);
    }

    public function checkAttachedImagesAltTexts ($valid, $value, $field, $name) {
        global $_POST;

        $altIsEmpty = false;
        if ($this->postHasFeaturedImage($field, $_POST)) {
            $altIsEmpty = empty($this->getAltText($_POST['_thumbnail_id'])) ? "Please add an alt text to the featured image." : false;
        }
        
        if ($this->imageFieldHasValue($field, $value)) {
            $altIsEmpty = empty($this->getAltText($value)) ? "Please add alt texts to the images." : false;
        }

        if ($altIsEmpty) {
            acf_add_validation_error($_POST['acf']['field_654a2a58ca4e9'], $altIsEmpty );
            return __('Please add an alt text to the image', 'municipio');
        }

        return $valid;
    }

    private function postHasFeaturedImage($field, $post) {
        return !empty($field['key']) && $field['key'] == 'field_654a2a58ca4e9' && !empty($post) && !empty($post['_thumbnail_id']) && $post['_thumbnail_id'] !== '-1';
    }

    private function imageFieldHasValue($field, $value) {
        return !empty($field['type']) && $field['type'] == 'image' && !empty($value) && is_numeric($value);
    }

    private function getAltText($id) {
        return get_post_meta($id, '_wp_attachment_image_alt', true);
    }
}
