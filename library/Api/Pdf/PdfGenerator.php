<?php

namespace Municipio\Api\Pdf;

use Municipio\Api\RestApiEndpoint;
use WP_REST_Request;
use WP_REST_Response;

class PdfGenerator extends RestApiEndpoint
{
    private const NAMESPACE = 'pdf/v2';
    private const ROUTE = '/id=(?P<id>\d+(?:,\d+)*)';
    private $defaultPrefix = 'default';
    
    public function __construct()
    {
        add_action('init', array($this, 'addAcfFields'));
    }

    public function handleRegisterRestRoute(): bool
    {
        return register_rest_route(self::NAMESPACE, self::ROUTE, array(
            'methods' => 'GET',
            'callback' => array($this, 'handleRequest'),
        ));
    }

    public function handleRequest(WP_REST_Request $request): WP_REST_Response
    {
        $ids = $request->get_param('id');
        if (!empty($ids) && is_string($ids)) {
            $idArr = explode(',', $ids);
            $pdf = new \Municipio\Api\Pdf\CreatePdf();
            return $pdf->renderView($idArr);
        }
    }

    public function addAcfFields () {
        $postTypes = get_post_types([
                'public' => true
        ], 'objects');
        
        if (!empty($postTypes) && is_array($postTypes) && function_exists('acf_add_local_field_group')) {
            acf_add_local_field_group(array(
                'key' => 'group_pdf_generator_emblem',
                'title' => __('Emblem', 'municipio'),
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
            'key' => 'group_pdf_generator_templates',
            'title' => __('PDF post types', 'municipio'),
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
        }
    }

    private function getFieldsForEachPostType($postTypes) {
        $fields = [];

        array_unshift($postTypes, (object) [
            'name' => 'default',
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

    private function structurePostTypesArray($postTypes, $currentPostType) {
        $postTypesArray = [];
        foreach ($postTypes as $postType) {
            if (!empty($postType->name) && !empty($postType->label) && $postType->name != $currentPostType && $postType->name != $this->defaultPrefix) {
                $postTypesArray[$postType->name] = $postType->label;
            }
        }
        return $postTypesArray;
    }

    private function excludedPostTypes($postTypeName) {
        return !in_array($postTypeName, ['attachment']);
    }
}
