<?php

namespace Modularity\Module\Gallery;

use Modularity\Integrations\Component\ImageResolver;
use ComponentLibrary\Integrations\Image\Image as ImageComponentContract;
class Gallery extends \Modularity\Module
{
    public $slug = 'gallery';
    public $supports = array();

    public function init()
    {
        $this->nameSingular = __('Gallery', 'modularity');
        $this->namePlural = __('Galleries', 'modularity');
        $this->description =  __('Outputs a gallery with images', 'modularity');

        $this->acfFields();
    }

    /**
     * @return array
     */
    public function data() : array
    {
        $data = $this->getFields();

        $data['ariaLabels'] = (object) [
            'prev' => __('Previous slide','modularity'),
            'next' => __('Next slide', 'modularity'),
        ];

        if ($data['mod_gallery_images']) {
            $data['mod_gallery_images'] = $this->getThumbnails($data['mod_gallery_images']);
            foreach ($data['mod_gallery_images'] as $i => $image) {

                $data['images'][$i]['image'] = ImageComponentContract::factory(
                    (int) $image['id'],
                    [768, 432],
                    new ImageResolver()
                );

                $data['images'][$i]['largeImage']  = $image["sizes"]["large"];
                $data['images'][$i]['smallImage']  = $image["sizes"]["thumbnail"];
                $data['images'][$i]['alt']  = $image["description"];
                $data['images'][$i]['caption']  = $image["caption"];
            }

        } else {
            $data['images'] = null;
        }

        return $data;
    }


    private function getThumbnails($images)
    {
        foreach ($images as &$image) {
            $thumbnail = wp_get_attachment_image_src(
                $image['id'],
                apply_filters(
                    'modularity/image/gallery/thumbnail',
                    municipio_to_aspect_ratio('1:1', array(300, 300)),
                    $this->args
                )
            );
            $image['sizes']['thumbnail'] = $thumbnail[0];
            $image['sizes']['thumbnail-width'] = $thumbnail[1];
            $image['sizes']['thumbnail-height'] = $thumbnail[2];
        }

        return $images;
    }

    /**
     * ACF Fields for admin
     */
    public function acfFields()
    {
        if (function_exists('acf_add_local_field_group')) {
            acf_add_local_field_group(array (
                'key' => 'group_5666af6d26b7c',
                'title' => 'Gallery',
                'fields' => array (
                    array (
                        'key' => 'field_5666af72e3194',
                        'label' => 'Images',
                        'name' => 'mod_gallery_images',
                        'type' => 'gallery',
                        'instructions' => '',
                        'required' => 1,
                        'conditional_logic' => 0,
                        'wrapper' => array (
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'min' => '',
                        'max' => '',
                        'preview_size' => 'thumbnail',
                        'library' => 'all',
                        'min_width' => '',
                        'min_height' => '',
                        'min_size' => '',
                        'max_width' => '',
                        'max_height' => '',
                        'max_size' => '',
                        'mime_types' => '',
                    ),
                ),
                'location' => array (
                    array (
                        array (
                            'param' => 'post_type',
                            'operator' => '==',
                            'value' => 'mod-gallery',
                        ),
                    ),
                ),
                'menu_order' => 0,
                'position' => 'normal',
                'style' => 'default',
                'label_placement' => 'top',
                'instruction_placement' => 'label',
                'hide_on_screen' => '',
                'active' => 1,
                'description' => '',
            ));
        }
    }

    /**
     * Available "magic" methods for modules:
     * init()            What to do on initialization
     * data()            Use to send data to view (return array)
     * style()           Enqueue style only when module is used on page
     * script            Enqueue script only when module is used on page
     * adminEnqueue()    Enqueue scripts for the module edit/add page in admin
     * template()        Return the view template (blade) the module should use when displayed
     */
}
