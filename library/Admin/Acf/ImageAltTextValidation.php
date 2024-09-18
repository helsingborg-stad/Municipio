<?php

namespace Municipio\Admin\Acf;

class ImageAltTextValidation
{
    public function __construct()
    {
        //Filter for hidden field, to check if the featured image has an alt text
        add_filter('acf/validate_value/key=field_654a2a58ca4e9', array(
            $this,
            'checkFeaturedImageField'
        ), 10, 4);

        //Filter for standard image field
        add_filter('acf/validate_value/type=image', array(
            $this,
            'checkImageField'
        ), 10, 4);

        //Filter for focus image field
        add_filter('acf/validate_value/type=focuspoint', array(
            $this,
            'checkFocusField'
        ), 10, 4);
    }

    /**
     * Checks if the image field has an alt text.
     *
     * @param bool $valid The current validation status.
     * @param mixed $value The value of the field.
     * @param array $field The field array.
     * @param string $input The input name.
     * @return bool Returns true if the image field has an alt text, false otherwise.
     */
    public function checkImageField($valid, $value, $field, $input)
    {
        if ($this->imageFieldHasValue($field, $value)) {
            $fieldImageAltText = $this->getAltText($value);

            if (is_null($fieldImageAltText)) {
                return $this->lang()->altTextSingular;
            }
        }
        return $valid;
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
        if (empty($value) || !is_numeric($value)) {
            return false;
        }
        return true;
    }

    /**
     * Checks if the focus field has an alt text.
     *
     * @param bool $valid The current validation status.
     * @param mixed $value The value of the field.
     * @param array $field The field array.
     * @param string $input The input name.
     * @return bool Returns true if the focus field has an alt text, false otherwise.
     */
    public function checkFocusField($valid, $value, $field, $input)
    {
        if ($this->focusFieldHasValue($field, $value)) {
            $fieldImageAltText = $this->getAltText($value['id']);

            if (is_null($fieldImageAltText)) {
                return $this->lang()->altTextSingular;
            }
        }
        return $valid;
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
        if (empty($value) || !is_array($value)) {
            return false;
        }

        if (empty($value['id'])) {
            return false;
        }

        if (!is_numeric($value['id'])) {
            return false;
        }

        return true;
    }

    /**
     * Retrieves the post data.
     *
     * @return array|null The post data, or null if it is empty.
     */
    private function getPostData(): ?array
    {
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
    public function checkFeaturedImageField($valid, $value, $field, $name)
    {

        //Get post data
        $postData = $this->getPostData();

        //Not a post action
        if (empty($postData) || !is_array($postData)) {
            return $valid;
        }

        if ($this->postHasFeaturedImage($postData)) {
            $thumbnailID           = $postData['_thumbnail_id'];
            $thumbnailImageAltText = $this->getAltText($thumbnailID);

            if (is_null($thumbnailImageAltText)) {
                return $this->lang()->altTextFeaturedImage;
            }
        }

        return $valid;
    }

    /**
     * Checks if a post has a featured image.
     *
     * @param array $field The field array.
     * @param array $post The post array.
     * @return bool Returns true if the post has a featured image, false otherwise.
     */
    private function postHasFeaturedImage($postData)
    {
        if (empty($postData) || empty($postData['_thumbnail_id'])) {
            return false;
        }

        if ($postData['_thumbnail_id'] === '-1') {
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

    /**
     * Retrieves the language strings.
     *
     * @return object The language strings.
     */
    private function lang(): object
    {
        return (object) [
            'altTextFeaturedImage' => __("Please add an alt text to the featured image.", 'municipio'),
            'altTextSingular'      => __("Please add an alt text to the image.", 'municipio')
        ];
    }
}
