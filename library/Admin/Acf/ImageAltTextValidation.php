<?php

namespace Municipio\Admin\Acf;

class ImageAltTextValidation
{
    public function __construct()
    {
        add_action( 'acf/validate_value', array($this, 'checkAttachedImagesAltTexts'), 10, 4);
    }

    /**
     * Retrieves the language strings.
     *
     * @return object The language strings.
     */
    private function lang(): object {
        return (object) [
            'altTextFeaturedImage'  => __("Please add an alt text to the featured image.", 'municipio'),
            'altTextSingular'       => __("Please add an alt text to the image.", 'municipio')
        ];
    }

    /**
     * Retrieves the post data.
     *
     * @return array|null The post data, or null if it is empty.
     */
    private function getPostData(): ?string {
        if (empty($_POST) || !is_array($_POST)) {
            return null;
        }
        return $_POST;
    }

    /**
     * Checks if the alt text is set for the featured image and the image field.
     *
     * @param bool $valid The current validation status.
     * @param mixed $value The value of the field.
     * @param array $field The field array.
     * @param string $name The field name.
     * @return bool Returns true if the alt text is set for the featured image and the image field, false otherwise.
     */
    public function checkAttachedImagesAltTexts($valid, $value, $field, $name) {
    
        //Declare vars.
        $validationErrorMessage = false;
        $postData               = $this->getPostData();

        //Not a post action
        if (empty($postData) || !is_array($postData)) {
            return;
        }

        //Featured image
        if ($this->postHasFeaturedImage($field, $postData)) {
            $thumbnailID            = $postData['_thumbnail_id'];
            $thumbnailImageAltText  = $this->getAltText($thumbnailID);

            if(is_null($thumbnailImageAltText)) {
                $validationErrorMessage = $this->lang()->altTextFeaturedImage;
            }
        }
        
        //Image field
        if ($this->imageFieldHasValue($field, $value)) {
            $fieldImageAltText = $this->getAltText($value);

            if(is_null($fieldImageAltText)) {
                $validationErrorMessage = $this->lang()->altTextSingular;
            }
        }

        //Focus field
        if ($this->focusFieldHasValue($field, $value)) {
            $fieldImageAltText = $this->getAltText($value['id']);

            if(is_null($fieldImageAltText)) {
                $validationErrorMessage = $this->lang()->altTextSingular;
            }
        }

        if ($validationErrorMessage !== false) {
            acf_add_validation_error(
                $_POST['acf']['field_654a2a58ca4e9'], 
                $validationErrorMessage
            );
        }
    }

    /**
     * Checks if a post has a featured image.
     *
     * @param array $field The field array.
     * @param array $post The post array.
     * @return bool Returns true if the post has a featured image, false otherwise.
     */
    private function postHasFeaturedImage($field, $post)
    {
        if(!isset($field['key'])) {
            return false;
        }

        if($field['key'] !== 'field_654a2a58ca4e9') {
            return false;
        }

        if(empty($post) || empty($post['_thumbnail_id'])) {
            return false;
        }

        if($post['_thumbnail_id'] === '-1') {
            return false;
        }

        return true;
    }

    /**
     * Checks if an image field has a valid value.
     *
     * @param array $field The image field array.
     * @param mixed $value The value of the image field.
     * @return bool Returns true if the image field has a valid value, false otherwise.
     */
    private function imageFieldHasValue($field, $value): bool
    {
        if($field['type'] !== 'image') {
            return false;
        }

        if(empty($value) || !is_numeric($value)) {
            return false;
        }

        return true;
    }

    /**
     * Checks if the focus field has a valid value.
     *
     * @param array $field The field array.
     * @param mixed $value The value of the field.
     * @return bool Returns true if the focus field has a valid value, false otherwise.
     */
    private function focusFieldHasValue($field, $value): bool
    {
        if($field['type'] !== 'focuspoint') {
            return false;
        }

        if(empty($value) || !is_array($value)) {
            return false;
        }

        if(empty($value['id']) || !is_numeric($value['id'])) {
            return false;
        }

        return true;
    }

    /**
     * Retrieves the alt text for an image attachment.
     *
     * @param int $id The ID of the image attachment.
     * @return string|null The alt text of the image attachment, or null if it is empty.
     */
    private function getAltText($id): ?string
    {
        $altText = get_post_meta($id, '_wp_attachment_image_alt', true);
        if (!empty($altText)) {
            return $altText;
        }
        return null;
    }
}
