<?php

namespace Municipio\Customizer\Sections;

use Municipio\Customizer\KirkiField;

/**
 * Archive section.
 */
class Archive
{
    public $sectionId;

    /**
     * Constructor.
     */
    public function __construct(string $sectionID, private object $archive)
    {
        KirkiField::addField([
            'type'     => 'text',
            'settings' => 'archive_' . $archive->name . '_heading',
            'label'    => esc_html__('Archive heading', 'municipio'),
            'section'  => $sectionID,
            'default'  => '',
            'output'   => [
                [
                    'type'      => 'controller',
                    'as_object' => true,
                ]
            ],
        ]);

        KirkiField::addField([
            'type'     => 'textarea',
            'settings' => 'archive_' . $archive->name . '_body',
            'label'    => esc_html__('Archive lead text', 'municipio'),
            'section'  => $sectionID,
            'default'  => '',
            'output'   => [
                [
                    'type'      => 'controller',
                    'as_object' => true,
                ]
            ],
        ]);

        KirkiField::addField([
            'type'     => 'select',
            'settings' => 'archive_' . $archive->name . '_style',
            'label'    => esc_html__('Style', 'municipio'),
            'section'  => $sectionID,
            'choices'  => $this->getArchiveViewChoices(),
            'default'  => 'cards',
            'output'   => [
                [
                    'type'      => 'controller',
                    'as_object' => true,
                ]
            ],
        ]);

        KirkiField::addField([
            'type'            => 'select',
            'settings'        => 'archive_' . $archive->name . '_format',
            'label'           => esc_html__('Format', 'municipio'),
            'section'         => $sectionID,
            'default'         => 'tall',
            'choices'         => [
                'tall'   => esc_html__('Tall', 'municipio'),
                'square' => esc_html__('Square', 'municipio')
            ],
            'output'          => [
                [
                    'type'      => 'controller',
                    'as_object' => true,
                ]
            ],
            'active_callback' => [
                [
                    'setting'  => 'archive_' . $archive->name . '_style',
                    'operator' => '==',
                    'value'    => 'grid',
                ]
            ],
        ]);

        KirkiField::addField([
            'type'            => 'switch',
            'settings'        => 'archive_' . $archive->name . '_display_featured_image',
            'label'           => esc_html__('Display featured image', 'municipio'),
            'section'         => $sectionID,
            'default'         => 1,
            'choices'         => [
                1 => esc_html__('Show', 'municipio'),
                0 => esc_html__('Hide', 'municipio'),
            ],
            'output'          => [
                [
                    'type'      => 'controller',
                    'as_object' => true,
                ]
            ],
            'active_callback' => [
                [
                    'setting'  => 'archive_' . $archive->name . '_style',
                    'operator' => '==',
                    'value'    => 'collection',
                ]
            ],
        ]);
        KirkiField::addField([
            'type'        => 'slider',
            'settings'    => 'archive_' . $archive->name . '_post_count',
            'label'       => esc_html__('Number of posts to display', 'municipio'),
            'description' => esc_html__('How many posts that should be displayed on each page.', 'municipio'),
            'section'     => $sectionID,
            'transport'   => 'refresh',
            'default'     => 9,
            'choices'     => [
                'min'  => 4,
                'max'  => 40,
                'step' => 1,
            ]
        ]);

        KirkiField::addField([
            'type'            => 'slider',
            'settings'        => 'archive_' . $archive->name . '_number_of_columns',
            'label'           => esc_html__('Number of columns to display', 'municipio'),
            'description'     => esc_html__('How many columns that the posts should be divided in.', 'municipio'),
            'section'         => $sectionID,
            'transport'       => 'refresh',
            'default'         => 4,
            'choices'         => [
                'min'  => 1,
                'max'  => 4,
                'step' => 1,
            ],
            'output'          => [
                [
                    'type'      => 'controller',
                    'as_object' => true,
                ]
            ],
            'active_callback' => [
                [
                    'setting'  => 'archive_' . $archive->name . '_style',
                    'operator' => '!=',
                    'value'    => 'list',
                ]
            ],
        ]);

        KirkiField::addField([
            'type'        => 'switch',
            'settings'    => 'archive_' . $archive->name . '_display_openstreetmap',
            'label'       => esc_html__('Display map', 'municipio'),
            'description' => esc_html__('Display an Open Street Map on the archive if applicable.', 'municipio'),
            'section'     => $sectionID,
            'default'     => 0,
            'choices'     => [
                1 => esc_html__('Show', 'municipio'),
                0 => esc_html__('Hide', 'municipio'),
            ],
            'output'      => [
                [
                    'type'      => 'controller',
                    'as_object' => true,
                ]
            ],
        ]);
        KirkiField::addField([
            'type'            => 'switch',
            'settings'        => 'archive_' . $archive->name . '_display_archive_loop',
            'label'           => esc_html__('Keep the regular loop', 'municipio'),
            'description'     => esc_html__('Keep the regular loop of posts on the archive?', 'municipio'),
            'section'         => $sectionID,
            'default'         => 0,
            'choices'         => [
                1 => esc_html__('Show', 'municipio'),
                0 => esc_html__('Hide', 'municipio'),
            ],
            'output'          => [
                [
                    'type'      => 'controller',
                    'as_object' => true,
                ]
            ],
            'active_callback' => [
                [
                    'setting'  => 'archive_' . $archive->name . '_display_openstreetmap',
                    'operator' => '==',
                    'value'    => 1,
                ]
            ],
        ]);
        KirkiField::addField([
            'type'            => 'switch',
            'settings'        => 'archive_' . $archive->name . '_display_google_maps_link',
            'label'           => esc_html__('Display Google Maps-link', 'municipio'),
            'description'     => esc_html__('Display a link to Google Maps on each marker on the map?', 'municipio'),
            'section'         => $sectionID,
            'default'         => 0,
            'choices'         => [
                1 => esc_html__('Show', 'municipio'),
                0 => esc_html__('Hide', 'municipio'),
            ],
            'output'          => [
                [
                    'type'      => 'controller',
                    'as_object' => true,
                ],
            ],
            'active_callback' => [
                [
                    'setting'  => 'archive_' . $archive->name . '_display_openstreetmap',
                    'operator' => '==',
                    'value'    => 1,
                ]
            ],
        ]);
        KirkiField::addField([
            'type'              => 'select',
            'settings'          => 'archive_' . $archive->name . '_enabled_filters',
            'label'             => esc_html__('Filters', 'municipio'),
            'description'       => esc_html__('Add filters to let the user browse partial content.', 'municipio'),
            'multiple'          => 6,
            'section'           => $sectionID,
            // Below prevents Kirki bugg from using faulty default sanitize_callback.
            'sanitize_callback' => fn($values) => $values,
            'default'           => [],
            'choices'           => array_merge(
                [
                    'text_search' => esc_html__('Text search', 'municipio'),
                    'date_range'  => esc_html__('Date range', 'municipio'),
                ],
                (array) isset($archive->taxonomies) && !empty($archive->taxonomies) ? $archive->taxonomies : []
            ),
            'output'            => [
                [
                    'type'      => 'controller',
                    'as_object' => true,
                ]
            ],
        ]);
        if (!empty($archive->taxonomies)) {
            foreach ($archive->taxonomies as $key => $label) :
                KirkiField::addField([
                'type'            => 'select',
                'settings'        => 'archive_' . $archive->name . '_' . $key . '_filter_field_type',
                'label'           => $label,
                'description'     => sprintf(esc_html__('What field type to use for the %s filter.', 'municipio'), $label),
                'section'         => $sectionID,
                'choices'         => [
                    'multi'  => esc_html__('Multiselect', 'municipio'),
                    'single' => esc_html__('Single select', 'municipio'),
                ],
                'default'         => 'multi',
                'output'          => [
                    [
                        'type'      => 'controller',
                        'as_object' => true,
                    ]
                ],
                'active_callback' => [
                    [
                        'setting'  => 'archive_' . $archive->name . '_enabled_filters',
                        'operator' => 'contains',
                        'value'    => $key,
                    ]
                ],
                ]);
            endforeach;
        }
        KirkiField::addField([
            'type'        => 'switch',
            'settings'    => 'archive_' . $archive->name . '_filter_type',
            'label'       => esc_html__('Facetting type', 'municipio'),
            'description' => esc_html__('Wheter to broaden/search (or/off) OR taper/filter (and/on) search result when adding multiple selections for facetting.', 'municipio'),
            'section'     => $sectionID,
            'default'     => 'or',
            'choices'     => [
                'or'  => esc_html__('OR', 'municipio'),
                'and' => esc_html__('AND', 'municipio'),
            ],
            'output'      => [
                [
                    'type'      => 'controller',
                    'as_object' => true,
                ]
            ]
        ]);

        if (!empty($archive->taxonomies)) {
            KirkiField::addField([
                'type'              => 'select',
                'settings'          => 'archive_' . $archive->name . '_taxonomies_to_display',
                'label'             => esc_html__('Taxonomy display', 'municipio'),
                'description'       => esc_html__('What taxonomies should be displayed?', 'municipio'),
                'multiple'          => 4,
                'section'           => $sectionID,
                'choices'           => $archive->taxonomies,
                // Below prevents Kirki bugg from using faulty default sanitize_callback.
                'sanitize_callback' => fn($values) => $values,
                'output'            => [
                    [
                        'type'      => 'controller',
                        'as_object' => true,
                    ]
                ],
            ]);
        }

        if (!empty($archive->orderBy)) {
            KirkiField::addField([
                'type'        => 'select',
                'settings'    => 'archive_' . $archive->name . '_order_by',
                'label'       => esc_html__('Order By', 'municipio'),
                'description' => esc_html__('Select a key/value to order by.', 'municipio'),
                'section'     => $sectionID,
                'choices'     => $archive->orderBy,
                'default'     => 'post_date',
                'output'      => [
                    [
                        'type'      => 'controller',
                        'as_object' => true,
                    ]
                ],
            ]);

            KirkiField::addField([
                'type'        => 'select',
                'settings'    => 'archive_' . $archive->name . '_order_direction',
                'label'       => esc_html__('Order direction', 'municipio'),
                'description' => esc_html__('Select decending or ascending order.', 'municipio'),
                'section'     => $sectionID,
                'default'     => 'desc',
                'choices'     => [
                    'asc'  => __("Ascending", 'municipio'),
                    'desc' => __("Decending", 'municipio')
                ],
                'output'      => [
                    [
                        'type'      => 'controller',
                        'as_object' => true,
                    ]
                ],
            ]);
        }

        if (!empty($archive->dateSource)) {
            KirkiField::addField([
                'type'            => 'select',
                'settings'        => 'archive_' . $archive->name . '_date_field',
                'label'           => esc_html__('Date', 'municipio'),
                'description'     => esc_html__('Select source of timestamp for post', 'municipio'),
                'section'         => $sectionID,
                'default'         => 'none',
                'choices'         => array_merge(
                    ['none' => esc_html__('Hide date', 'municipio')],
                    $archive->dateSource
                ),
                'output'          => [
                    [
                        'type'      => 'controller',
                        'as_object' => true,
                    ]
                ],
                'active_callback' => [
                    [
                        'setting'  => 'archive_' . $archive->name . '_style',
                        'operator' => '!=',
                        'value'    => 'list',
                    ]
                ],
            ]);

            KirkiField::addField([
                'type'            => 'select',
                'settings'        => 'archive_' . $archive->name . '_date_format',
                'label'           => esc_html__('Date format', 'municipio'),
                'description'     => esc_html__('In what format to display date', 'municipio'),
                'section'         => $sectionID,
                'default'         => 'date',
                'choices'         => [
                    'date'       => esc_html__('Date', 'municipio'),
                    'date-time'  => esc_html__('Date, Time', 'municipio'),
                    'date-badge' => esc_html__('Date badge', 'municipio')
                ],
                'output'          => [
                    [
                        'type'      => 'controller',
                        'as_object' => true,
                    ]
                ],
                'active_callback' => [
                    [
                        'setting'  => 'archive_' . $archive->name . '_date_field',
                        'operator' => '!=',
                        'value'    => 'none',
                    ],
                    [
                        'setting'  => 'archive_' . $archive->name . '_style',
                        'operator' => '!=',
                        'value'    => 'list',
                    ]
                ],
            ]);
        }
        KirkiField::addField([
            'type'        => 'switch',
            'settings'    => 'archive_' . $archive->name . '_reading_time',
            'label'       => esc_html__('Display reading time', 'municipio'),
            'description' => esc_html__('Wether to display the estimated reading time for each post', 'municipio'),
            'section'     => $sectionID,
            'default'     => 0,
            'choices'     => [
                1 => esc_html__('Show', 'municipio'),
                0 => esc_html__('Hide', 'municipio'),
            ],
            'output'      => [
                [
                    'type'      => 'controller',
                    'as_object' => true,
                ]
            ]
        ]);
    }

    /**
     * Get the archive view choices
     *
     * @return array
     */
    private function getArchiveViewChoices(): array
    {
        $appearanceOptions = [
            'box'        => esc_html__('Box', 'municipio'),
            'compressed' => esc_html__('Compressed', 'municipio'),
            'cards'      => esc_html__('Cards', 'municipio'),
            'newsitem'   => esc_html__('News', 'municipio'),
            'list'       => esc_html__('List', 'municipio'),
            'grid'       => esc_html__('Blocks', 'municipio'),
            'collection' => esc_html__('Collection', 'municipio'),
            'segment'    => esc_html__('Segment', 'municipio'),
        ];

        $schemaType = \Municipio\SchemaData\Helper\GetSchemaType::getSchemaTypeFromPostType($this->archive->name);

        if ($schemaType) {
            $appearanceOptions = array_merge(['schema' => esc_html__('Schema', 'municipio')], $appearanceOptions);
        }

        return $appearanceOptions;
    }
}
