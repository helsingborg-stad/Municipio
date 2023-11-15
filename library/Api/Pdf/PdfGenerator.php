<?php

namespace Municipio\Api\Pdf;

use Municipio\Api\RestApiEndpoint;
use WP_REST_Request;
use WP_REST_Response;

class PdfGenerator extends RestApiEndpoint
{
    private const NAMESPACE = 'pdf/v2';
    private const ROUTE = '/id=(?P<id>\d+(?:,\d+)*)';
    
    public function __construct()
    {
        add_action('init', array($this, 'acfTest'));
    }

    public function acfTest () {
        $postTypes = get_post_types([
                'public' => true
        ], 'objects');
        
        if (!empty($postTypes) && is_array($postTypes) && function_exists('acf_add_local_field_group')) {
            acf_add_local_field_group(array(
            'key' => 'group_123321123',
            'title' => __('Pdf Generator', 'municipio'),
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
            }
        }

        return $fields;
    }

    private function excludedPostTypes($postTypeName) {
        return !in_array($postTypeName, ['attachment']);
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
}
