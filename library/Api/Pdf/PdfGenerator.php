<?php

namespace Municipio\Api\Pdf;

use Municipio\Api\RestApiEndpointsRegistry;

class PdfGenerator
{    
    private $defaultPrefix = 'default';

    public function __construct() {
        RestApiEndpointsRegistry::add(new \Municipio\Api\Pdf\PdfIdEndpoint());
        RestApiEndpointsRegistry::add(new \Municipio\Api\Pdf\PdfArchiveEndpoint());

        add_action('init', array($this, 'addAcfToPdfGeneratorOptionsPage'), 99);
        add_filter('Municipio/Accessibility/Items', array($this, 'replacePrintWithPdf'));
    }

    /**
     * Replaces the default Print with PDF generator in accessibility items.
     *
     * @param array $items Original accessibility items.
     *
     * @return array Modified accessibility items.
     */
    public function replacePrintWithPdf($items) {
        $replacePrintWithPdf = get_field('field_pdf_replace_print', 'option');
        if (!empty($replacePrintWithPdf) && $typeOfPage = $this->typeOfPage()) {
            $items['print'] = [
                'icon' => 'print',
                'href' => '#',
                'attributeList' => [
                    'data-js-pdf-generator' => $typeOfPage,
                ],
                'text' => __('Print', 'municipio'),
                'label' => __('Print this page', 'municipio')
            ];
        }

        return $items;
    }

    /**
     * Determines the type of page (single, page, archive).
     *
     * @return string|false Page type or false if unknown.
     */
    private function typeOfPage() {
        if (is_single() || is_page()) {
            return 'single';
        }

        if (is_archive()) {
            return 'archive';
        }

        return false;
    }

     /**
     * Adds ACF fields to the PDF generator options page.
     */
    public function addAcfToPdfGeneratorOptionsPage() {
        $postTypes = get_post_types([
                'public' => true
        ], 'objects');
        
        if (!empty($postTypes) && is_array($postTypes) && function_exists('acf_add_local_field_group')) {
            acf_add_local_field_group(array(
                'key' => 'group_pdf_generator_replace_print',
                'title' => __('Settings', 'municipio'),
                'fields' => [
                    [
                        'key' => 'field_pdf_replace_print',
                        'label' => __('Replace default Print with PDF generator', 'municipio'),
                        'name' => 'replace_print',
                        'type' => 'true_false',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'message' => '',
                        'default_value' => 0,
                        'ui_on_text' => '',
                        'ui_off_text' => '',
                        'ui' => 1,
                    ]
                ],
                'location' => array(
                    0 => array(
                        0 => array(
                            'param' => 'options_page',
                            'operator' => '==',
                            'value' => 'pdf-generator-settings',
                        ),
                    ),
                ),
                'menu_order' => 0,
                'position' => 'normal',
                'style' => 'default',
                'label_placement' => 'left',
                'instruction_placement' => 'label',
                'hide_on_screen' => '',
                'active' => true,
                'description' => '',
                'show_in_rest' => 0,
                'acfe_display_title' => '',
                'acfe_autosync' => array(
                    0 => 'json',
                ),
                'acfe_form' => 0,
                'acfe_meta' => '',
                'acfe_note' => '',
            ));

            acf_add_local_field_group(array(
                'key' => 'group_pdf_generator_emblem',
                'title' => __('General cover settings', 'municipio'),
                'fields' => [
                    [
                        'key' => 'field_pdf_emblem',
                        'label' => __('Emblem', 'municipio'),
                        'name' => 'pdf_frontpage_emblem',
                        'type' => 'image',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'uploader' => '',
                        'acfe_thumbnail' => 0,
                        'return_format' => 'id',
                        'min_width' => '',
                        'min_height' => '',
                        'min_size' => '',
                        'max_width' => '',
                        'max_height' => '',
                        'max_size' => '',
                        'mime_types' => '',
                        'preview_size' => 'medium',
                        'library' => 'all',
                    ]
                ],
                'location' => array(
                    0 => array(
                        0 => array(
                            'param' => 'options_page',
                            'operator' => '==',
                            'value' => 'pdf-generator-settings',
                        ),
                    ),
                ),
                'menu_order' => 2,
                'position' => 'normal',
                'style' => 'default',
                'label_placement' => 'left',
                'instruction_placement' => 'label',
                'hide_on_screen' => '',
                'active' => true,
                'description' => '',
                'show_in_rest' => 0,
                'acfe_display_title' => '',
                'acfe_autosync' => array(
                    0 => 'json',
                ),
                'acfe_form' => 0,
                'acfe_meta' => '',
                'acfe_note' => '',
            ));

            acf_add_local_field_group(array(
            'key' => 'group_pdf_generator_templates',
            'title' => __('Specific cover settings', 'municipio'),
            'fields' => $this->getFieldsForEachPostType($postTypes),
            'location' => array(
                0 => array(
                    0 => array(
                        'param' => 'options_page',
                        'operator' => '==',
                        'value' => 'pdf-generator-settings',
                    ),
                ),
            ),
            'menu_order' => 3,
            'position' => 'normal',
            'style' => 'default',
            'label_placement' => 'left',
            'instruction_placement' => 'label',
            'hide_on_screen' => '',
            'active' => true,
            'description' => '',
            'show_in_rest' => 0,
            'acfe_display_title' => '',
            'acfe_autosync' => array(
                0 => 'json',
            ),
            'acfe_form' => 0,
            'acfe_meta' => '',
            'acfe_note' => '',
        ));
        }
    }

    /**
     * Retrieves ACF fields for each post type.
     *
     * @param array $postTypes Array of post types.
     *
     * @return array ACF fields.
     */
    private function getFieldsForEachPostType($postTypes) {
        $fields = [];

        array_unshift($postTypes, (object) [
            'name' => $this->defaultPrefix,
            'label' => __('Default', 'municipio')
        ]);
        
        foreach ($postTypes as $postType) {
            if (!empty($postType->name) && !empty($postType->label) && $this->excludedPostTypes($postType->name)) {
                $fields[] = [
                    'key' => 'field_tab_' . $postType->name,
                    'label' => $postType->label,
                    'name' => '',
                    'type' => 'tab',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'placement' => 'top',
                    'endpoint' => 0,
                ];
    
                $fields[] = [
                    'key' => 'field_heading_' . $postType->name,
                    'label' => __('Heading', 'municipio'),
                    'name' => $postType->name . '_pdf_frontpage_heading',
                    'type' => 'text',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                ];

                $fields[] = [
                    'key' => 'field_introduction_' . $postType->name,
                    'label' => __('Introduction', 'municipio'),
                    'name' => $postType->name . '_pdf_frontpage_introduction',
                    'type' => 'wysiwyg',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'default_value' => '',
                    'delay' => 0,
                    'tabs' => 'visual',
                    'toolbar' => 'basic',
                    'media_upload' => 0,
                ];

                $fields[] = [
                    'key' => 'field_cover_' . $postType->name,
                    'label' => __('Cover', 'municipio'),
                    'name' => $postType->name . '_pdf_frontpage_cover',
                    'type' => 'image',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'uploader' => '',
                    'acfe_thumbnail' => 0,
                    'return_format' => 'id',
                    'min_width' => '',
                    'min_height' => '',
                    'min_size' => '',
                    'max_width' => '',
                    'max_height' => '',
                    'max_size' => '',
                    'mime_types' => '',
                    'preview_size' => 'medium',
                    'library' => 'all',
                ];

                $fields[] = [
                    'key' => 'field_fallback_frontpage_' . $postType->name,
                    'label' => __('Default frontpage', 'municipio'),
                    'name' => $postType->name . '_pdf_fallback_frontpage',
                    'type' => 'radio',
                    'instructions' => __('If there is no data attached. Which frontpage should it use?', 'municipio'),
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'choices' => array(
                        'default' => __('Default', 'municipio'),
                        'none' => __('None', 'municipio'),
                        'custom' => __('Custom', 'municipio'),
                    ),
                    'default_value' => __('default', 'municipio'),
                    'return_format' => 'value',
                    'allow_null' => 0,
                    'other_choice' => 0,
                    'layout' => 'horizontal',
                    'save_other_choice' => 0,
                ];

                $fields[] = [
                    'key' => 'field_custom_frontpage_' . $postType->name,
                    'label' => __('Choose another frontpage', 'municipio'),
                    'name' => $postType->name . '_pdf_custom_frontpage',
                    'type' => 'select',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => array(
                        0 => array(
                            0 => array(
                                'field' => 'field_fallback_frontpage_' . $postType->name,
                                'operator' => '==',
                                'value' => 'custom',
                            ),
                        ),
                    ),
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'choices' => $this->structurePostTypesArray($postTypes, $postType->name),
                    'default_value' => 1,
                    'return_format' => 'value',
                    'multiple' => 0,
                    'allow_null' => 0,
                    'ui' => 0,
                    'ajax' => 0,
                    'placeholder' => '',
                ];
            }
        }

        return $fields;
    }

    /**
     * Structures an array of post types for ACF choices.
     *
     * @param array  $postTypes        Array of post types.
     * @param string $currentPostType  Current post type.
     *
     * @return array ACF choices.
     */
    private function structurePostTypesArray($postTypes, $currentPostType) {
        $postTypesArray = [];
        foreach ($postTypes as $postType) {
            if (!empty($postType->name) && !empty($postType->label) && $postType->name != $currentPostType && $postType->name != $this->defaultPrefix) {
                $postTypesArray[$postType->name] = $postType->label;
            }
        }
        return $postTypesArray;
    }

    
    /**
     * Checks if a post type should be excluded.
     * 
     * @param string $postTypeName Post type name.
     * 
     * @return bool Whether the post type should be excluded.
     */
    private function excludedPostTypes($postTypeName)
    {
        return !in_array($postTypeName, ['attachment']);
    }

}
