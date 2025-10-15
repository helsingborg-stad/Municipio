<?php

namespace Modularity\Module\Image;

use Modularity\Integrations\Component\ImageResolver;
use Modularity\Integrations\Component\ImageFocusResolver;
use ComponentLibrary\Integrations\Image\Image as ImageComponentContract;

class Image extends \Modularity\Module
{
    public $slug = 'image';
    public $supports = array();
    public $isBlockCompatible = false;

    /**
     * Init the module, give it a name etc.
     * @return void
     */
    public function init()
    {
        $this->nameSingular = __('Image', 'modularity');
        $this->namePlural = __('Images', 'modularity');
        $this->description = __('Outputs an image', 'modularity');
        add_filter('acf/load_field/key=field_570770b8e2e61', [$this, 'filterImageField']);
    }

    /**
     * Setup data
     * @return array
     */
    public function data() : array
    {

        //Declare default data
        $data = [
            'image' => false,
            'link' => false,
            'caption' => false,
            'byline' => false,
        ];

        // Get field definition
        $fields = $this->getFields();

        // If the image is set, get the image data
        $imageId = $fields['mod_image_image'] ?? null;
        if(is_array($imageId)) {
            $imageId = $imageId['ID'] ?? null;
        }

        if(is_numeric($imageId) && wp_attachment_is_image($imageId)) {
            $data['image'] = ImageComponentContract::factory(
                $imageId,
                [1920, false],
                new ImageResolver(),
                new ImageFocusResolver(['id' => $imageId])
            );

            $data['caption']    = $this->getImageCaption($fields, $imageId);
            $data['byline']     = $this->getImageByline($imageId);
        }

        //Get image link, if image is set and link is set
        $data['link'] = $data['image'] && $this->imageHasLink($fields) ? $fields['mod_image_link_url'] : false;

        return $data;
    }

    /**
     * If the image should be a link or not.
     * 
     * @param array $fields All the acf fields
     * @param array $imageId The image id
     * @return string|false
     */
    private function getImageCaption(array $fields, int $imageId) {
        $caption = wp_get_attachment_caption($imageId);
        if (!empty($fields['mod_image_caption'])) {
            $caption = $fields['mod_image_caption'];
        }
        return strip_tags($caption);
    }

    /**
     * Get the byline for the image
     * 
     * @param int $imageId The image id
     * @return string|false
     */
    private function getImageByline($imageId) {
        $meta = wp_get_attachment_metadata($imageId);
        if (isset($meta['image_meta']['byline'])) {
            return $meta['image_meta']['byline'];
        }
        return false;
    }

    /**
     * If the image should be a link or not.
     * 
     * @param array $fields All the acf fields
     * @return bool
     */
    private function imageHasLink(array $fields) {
        return !empty($fields['mod_image_link']) && $fields['mod_image_link'] != "false" && !empty($fields['mod_image_link_url']);
    }

    /**
     * Filter the image field to only allow certain mime types
     * @param array $field The acf field
     * @return array The modified acf field
     */
     public function filterImageField($field)
    {
        $imageMimeTypes = $this->getAllowedImageMimeTypes();
        $field['mime_types'] = $imageMimeTypes;
        $field['instructions'] = sprintf(
            __('Allowed file types: %s', 'modularity'),
            $imageMimeTypes
        );

        return $field;
    }

    /**
     * Get allowed image mime types
     * 
     * @return string Comma separated list of allowed image mime types
     */
    private function getAllowedImageMimeTypes()
    {
        static $allowedMimeTypes = null;

        if (!empty($allowedMimeTypes)) {
            return $allowedMimeTypes;
        }

        $allAllowedMimeTypes = get_allowed_mime_types();

        $allowedImageMimeTypes = array_filter($allAllowedMimeTypes, function($type) {
            return strpos($type, 'image/') === 0;
        });

        $structuredArray = [];
        foreach ($allowedImageMimeTypes as $key => $type) {
            foreach (explode('|', $key) as $ext) {
                $structuredArray[] = $ext;
            }
        }

        $allowedMimeTypes = implode(', ', $structuredArray);

        return $allowedMimeTypes;
    }

    /**
     * Choose appropriate style
     * @return string
     */

    public function template()
    {
        return 'default.blade.php';
    }
}
