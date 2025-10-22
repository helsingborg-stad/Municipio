<?php

namespace Municipio\Api\Pdf;

use Municipio\Api\RestApiEndpointsRegistry;
use Municipio\Api\Pdf\PdfHelper;

/**
 * PdfGenerator Class
 */
class PdfGenerator
{
    private $defaultPrefix = 'default';
    private PdfHelperInterface $pdfHelper;

    /**
     * PdfGenerator constructor.
     *
     * @param PdfHelperInterface $pdfHelper PdfHelper instance.
     */
    public function __construct(PdfHelperInterface $pdfHelper)
    {
        $this->pdfHelper = $pdfHelper;
        RestApiEndpointsRegistry::add(new \Municipio\Api\Pdf\PdfIdEndpoint());
        RestApiEndpointsRegistry::add(new \Municipio\Api\Pdf\PdfArchiveEndpoint());
    }

    /**
     * Add hooks for actions and filters.
     */
    public function addHooks(): void
    {
        add_action('init', array($this, 'addAcfToPdfGeneratorOptionsPage'), 99);
        add_filter('Municipio/Accessibility/Items', array($this, 'replacePrintWithPdf'));
        add_action('admin_notices', array($this, 'displayMissingSuggestedDependenciesNotices'));
    }

    /**
     * Display admin notices for missing suggested dependencies.
     */
    public function displayMissingSuggestedDependenciesNotices(): void
    {

        $systemMissingSuggestedDependencies = $this->pdfHelper->systemHasSuggestedDependencies() === false;

        if ($this->currentPageIsSettingsPage() && $systemMissingSuggestedDependencies) {
            $message = __('The PDF generator is missing some suggested dependencies. Please install the following PHP extensions: GD', 'municipio');
            wp_admin_notice($message, ['type' => 'warning']);
        }
    }

    /**
     * Check if the current page is the PDF generator settings page.
     *
     * @return bool Whether the current page is the PDF generator settings page.
     */
    private function currentPageIsSettingsPage(): bool
    {
        return is_admin() && isset($_GET['page']) && $_GET['page'] === 'pdf-generator-settings';
    }

    /**
     * Replaces the default Print with PDF generator in accessibility items.
     *
     * @param array $items Original accessibility items.
     *
     * @return array Modified accessibility items.
     */
    public function replacePrintWithPdf($items)
    {
        if ($this->shouldReplacePrintWithPdf()) {
            $items['print'] = [
                'icon'          => 'print',
                'href'          => '#',
                'attributeList' => [
                    'data-js-pdf-generator' => $this->typeOfPage(),
                ],
                'text'          => __('Print', 'municipio'),
                'label'         => __('Print this page', 'municipio')
            ];
        }

        return $items;
    }

    /**
     * Determines whether the default Print should be replaced with PDF generator.
     *
     * @return bool Whether the default Print should be replaced with PDF generator.
     */
    private function shouldReplacePrintWithPdf(): bool
    {
        $replacePrintWithPdf           = get_field('field_pdf_replace_print', 'option');
        $keepOriginalPrintForPostTypes = get_field('field_pdf_keep_regular_print', 'option');
        $typeOfPage                    = $this->typeOfPage();

        return
            !empty($replacePrintWithPdf) &&
            !in_array(get_post_type(), $keepOriginalPrintForPostTypes) &&
            $typeOfPage !== false;
    }

    /**
     * Determines the type of page (single, page, archive).
     *
     * @return string|false Page type or false if unknown.
     */
    private function typeOfPage()
    {
        $isSinglePageForPostType = get_option('page_for_' . get_post_type() . '_content');

        if (is_single() || is_page() || (!empty($isSinglePageForPostType) && $isSinglePageForPostType == 'on')) {
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
    public function addAcfToPdfGeneratorOptionsPage()
    {
        $postTypes = get_post_types([
                'public' => true
        ], 'objects');

        if (!empty($postTypes) && is_array($postTypes) && function_exists('acf_add_local_field_group')) {
            acf_add_local_field_group(array(
                'key'                   => 'group_pdf_generator_replace_print',
                'title'                 => __('Settings', 'municipio'),
                'fields'                => [
                    [
                        'key'               => 'field_pdf_replace_print',
                        'label'             => __('Replace default Print with PDF generator', 'municipio'),
                        'name'              => 'replace_print',
                        'type'              => 'true_false',
                        'instructions'      => '',
                        'required'          => 0,
                        'conditional_logic' => 0,
                        'wrapper'           => array(
                            'width' => '',
                            'class' => '',
                            'id'    => '',
                        ),
                        'message'           => '',
                        'default_value'     => 0,
                        'ui_on_text'        => '',
                        'ui_off_text'       => '',
                        'ui'                => 1,
                    ],
                    [
                        'key'               => 'field_pdf_keep_regular_print',
                        'label'             => __('Do not use PDF generator on following post types.', 'municipio'),
                        'name'              => 'pdf_keep_regular_print',
                        'type'              => 'checkbox',
                        'instructions'      => __('Checked post types will not use the PDF generator and will use the built in print function instead.', 'municipio'),
                        'required'          => 0,
                        'conditional_logic' => 0,
                        'wrapper'           => array(
                            'width' => '',
                            'class' => '',
                            'id'    => '',
                        ),
                        'choices'           => $this->postTypesToChoices($postTypes),
                        'return_format'     => 'value',
                        'allow_custom'      => 0,
                        'layout'            => 'horizontal',
                        'toggle'            => 0,
                        'save_custom'       => 0,
                    ],
                    [
                        'key'               => 'field_pdf_sort_posts_by_term',
                        'label'             => __('Sorts posts based on term', 'municipio'),
                        'name'              => 'pdf_sort_based_on_taxonomies',
                        'type'              => 'true_false',
                        'instructions'      => '',
                        'required'          => 0,
                        'conditional_logic' => 0,
                        'wrapper'           => array(
                            'width' => '',
                            'class' => '',
                            'id'    => '',
                        ),
                        'message'           => '',
                        'default_value'     => 0,
                        'ui_on_text'        => '',
                        'ui_off_text'       => '',
                        'ui'                => 1,
                    ],
                    [
                        'key'               => 'field_pdf_sort_posts_without_term_label',
                        'label'             => __('Label for posts missing terms', 'municipio'),
                        'name'              => 'pdf_sort_label_posts_without_term',
                        'type'              => 'text',
                        'instructions'      => __('When sorting, if a post is missing a term. It will fall under this label.', 'municipio'),
                        'required'          => 0,
                        'conditional_logic' => array(
                            0 => array(
                                0 => array(
                                    'field'    => 'field_pdf_sort_posts_by_term',
                                    'operator' => '==',
                                    'value'    => 1,
                                ),
                            ),
                        ),
                        'wrapper'           => array(
                            'width' => '',
                            'class' => '',
                            'id'    => '',
                        ),
                        'message'           => '',
                        'default_value'     => __('Other', 'municipio'),
                        'ui_on_text'        => '',
                        'ui_off_text'       => '',
                        'ui'                => 1,
                    ]
                ],
                'location'              => array(
                    0 => array(
                        0 => array(
                            'param'    => 'options_page',
                            'operator' => '==',
                            'value'    => 'pdf-generator-settings',
                        ),
                    ),
                ),
                'menu_order'            => 0,
                'position'              => 'normal',
                'style'                 => 'default',
                'label_placement'       => 'left',
                'instruction_placement' => 'label',
                'hide_on_screen'        => '',
                'active'                => true,
                'description'           => '',
                'show_in_rest'          => 0,
                'acfe_display_title'    => '',
                'acfe_autosync'         => array(
                    0 => 'json',
                ),
                'acfe_form'             => 0,
                'acfe_meta'             => '',
                'acfe_note'             => '',
            ));

            acf_add_local_field_group(array(
                'key'                   => 'group_pdf_generator_emblem',
                'title'                 => __('General cover settings', 'municipio'),
                'fields'                => [
                    [
                        'key'               => 'field_pdf_emblem',
                        'label'             => __('Emblem', 'municipio'),
                        'name'              => 'pdf_frontpage_emblem',
                        'type'              => 'image',
                        'instructions'      => '',
                        'required'          => 0,
                        'conditional_logic' => 0,
                        'wrapper'           => array(
                            'width' => '',
                            'class' => '',
                            'id'    => '',
                        ),
                        'uploader'          => '',
                        'acfe_thumbnail'    => 0,
                        'return_format'     => 'id',
                        'min_width'         => '',
                        'min_height'        => '',
                        'min_size'          => '',
                        'max_width'         => '',
                        'max_height'        => '',
                        'max_size'          => '',
                        'mime_types'        => '',
                        'preview_size'      => 'medium',
                        'library'           => 'all',
                    ]
                ],
                'location'              => array(
                    0 => array(
                        0 => array(
                            'param'    => 'options_page',
                            'operator' => '==',
                            'value'    => 'pdf-generator-settings',
                        ),
                    ),
                ),
                'menu_order'            => 2,
                'position'              => 'normal',
                'style'                 => 'default',
                'label_placement'       => 'left',
                'instruction_placement' => 'label',
                'hide_on_screen'        => '',
                'active'                => true,
                'description'           => '',
                'show_in_rest'          => 0,
                'acfe_display_title'    => '',
                'acfe_autosync'         => array(
                    0 => 'json',
                ),
                'acfe_form'             => 0,
                'acfe_meta'             => '',
                'acfe_note'             => '',
            ));

            acf_add_local_field_group(array(
            'key'                   => 'group_pdf_generator_templates',
            'title'                 => __('Specific cover settings', 'municipio'),
            'fields'                => $this->getFieldsForEachPostType($postTypes),
            'location'              => array(
                0 => array(
                    0 => array(
                        'param'    => 'options_page',
                        'operator' => '==',
                        'value'    => 'pdf-generator-settings',
                    ),
                ),
            ),
            'menu_order'            => 3,
            'position'              => 'normal',
            'style'                 => 'default',
            'label_placement'       => 'left',
            'instruction_placement' => 'label',
            'hide_on_screen'        => '',
            'active'                => true,
            'description'           => '',
            'show_in_rest'          => 0,
            'acfe_display_title'    => '',
            'acfe_autosync'         => array(
                0 => 'json',
            ),
            'acfe_form'             => 0,
            'acfe_meta'             => '',
            'acfe_note'             => '',
            ));
        }
    }

    /**
     * Generates an array of post type choices based on provided post types.
     *
     * @param array $postTypes An array of post type objects.
     *
     * @return array Associative array where keys are post type names and values are post type labels.
     */
    private function postTypesToChoices($postTypes)
    {
        $choices = [];

        foreach ($postTypes as $postType) {
            if (!empty($postType->name) && !empty($postType->label) && $this->excludedPostTypes($postType->name)) {
                $choices[$postType->name] = $postType->label;
            }
        }

        return $choices;
    }

    /**
     * Retrieves ACF fields for each post type.
     *
     * @param array $postTypes Array of post types.
     *
     * @return array ACF fields.
     */
    private function getFieldsForEachPostType($postTypes)
    {
        $fields = [];

        array_unshift($postTypes, (object) [
            'name'  => $this->defaultPrefix,
            'label' => __('Default', 'municipio')
        ]);

        foreach ($postTypes as $postType) {
            if (!empty($postType->name) && !empty($postType->label) && $this->excludedPostTypes($postType->name)) {
                $fields[] = [
                    'key'               => 'field_tab_' . $postType->name,
                    'label'             => $postType->label,
                    'name'              => '',
                    'type'              => 'tab',
                    'instructions'      => '',
                    'required'          => 0,
                    'conditional_logic' => 0,
                    'wrapper'           => array(
                        'width' => '',
                        'class' => '',
                        'id'    => '',
                    ),
                    'placement'         => 'top',
                    'endpoint'          => 0,
                ];

                $fields[] = [
                    'key'               => 'field_heading_' . $postType->name,
                    'label'             => __('Heading', 'municipio'),
                    'name'              => $postType->name . '_pdf_frontpage_heading',
                    'type'              => 'text',
                    'instructions'      => '',
                    'required'          => 0,
                    'conditional_logic' => 0,
                    'wrapper'           => array(
                        'width' => '',
                        'class' => '',
                        'id'    => '',
                    ),
                ];

                $fields[] = [
                    'key'               => 'field_introduction_' . $postType->name,
                    'label'             => __('Introduction', 'municipio'),
                    'name'              => $postType->name . '_pdf_frontpage_introduction',
                    'type'              => 'wysiwyg',
                    'instructions'      => '',
                    'required'          => 0,
                    'conditional_logic' => 0,
                    'wrapper'           => array(
                        'width' => '',
                        'class' => '',
                        'id'    => '',
                    ),
                    'default_value'     => '',
                    'delay'             => 0,
                    'tabs'              => 'visual',
                    'toolbar'           => 'basic',
                    'media_upload'      => 0,
                ];

                $fields[] = [
                    'key'               => 'field_cover_' . $postType->name,
                    'label'             => __('Cover', 'municipio'),
                    'name'              => $postType->name . '_pdf_frontpage_cover',
                    'type'              => 'image',
                    'instructions'      => '',
                    'required'          => 0,
                    'conditional_logic' => 0,
                    'wrapper'           => array(
                        'width' => '',
                        'class' => '',
                        'id'    => '',
                    ),
                    'uploader'          => '',
                    'acfe_thumbnail'    => 0,
                    'return_format'     => 'id',
                    'min_width'         => '',
                    'min_height'        => '',
                    'min_size'          => '',
                    'max_width'         => '',
                    'max_height'        => '',
                    'max_size'          => '',
                    'mime_types'        => '',
                    'preview_size'      => 'medium',
                    'library'           => 'all',
                ];

                $fields[] = [
                    'key'               => 'field_fallback_frontpage_' . $postType->name,
                    'label'             => __('Default frontpage', 'municipio'),
                    'name'              => $postType->name . '_pdf_fallback_frontpage',
                    'type'              => 'radio',
                    'instructions'      => __('If there is no data attached. Which frontpage should it use?', 'municipio'),
                    'required'          => 0,
                    'conditional_logic' => 0,
                    'wrapper'           => array(
                        'width' => '',
                        'class' => '',
                        'id'    => '',
                    ),
                    'choices'           => array(
                        'default' => __('Default', 'municipio'),
                        'none'    => __('None', 'municipio'),
                        'custom'  => __('Custom', 'municipio'),
                    ),
                    'default_value'     => __('default', 'municipio'),
                    'return_format'     => 'value',
                    'allow_null'        => 0,
                    'other_choice'      => 0,
                    'layout'            => 'horizontal',
                    'save_other_choice' => 0,
                ];

                $fields[] = [
                    'key'               => 'field_custom_frontpage_' . $postType->name,
                    'label'             => __('Choose another frontpage', 'municipio'),
                    'name'              => $postType->name . '_pdf_custom_frontpage',
                    'type'              => 'select',
                    'instructions'      => '',
                    'required'          => 0,
                    'conditional_logic' => array(
                        0 => array(
                            0 => array(
                                'field'    => 'field_fallback_frontpage_' . $postType->name,
                                'operator' => '==',
                                'value'    => 'custom',
                            ),
                        ),
                    ),
                    'wrapper'           => array(
                        'width' => '',
                        'class' => '',
                        'id'    => '',
                    ),
                    'choices'           => $this->structurePostTypesArray($postTypes, $postType->name),
                    'default_value'     => 1,
                    'return_format'     => 'value',
                    'multiple'          => 0,
                    'allow_null'        => 0,
                    'ui'                => 0,
                    'ajax'              => 0,
                    'placeholder'       => '',
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
    private function structurePostTypesArray($postTypes, $currentPostType)
    {
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
