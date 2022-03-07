<?php

namespace Municipio\Customizer\Sections;

class Archive
{
    public $sectionId;

    public function __construct($panelID, $archive)
    {
        //Create section_id
        $this->sectionId = $panelID . "_" . $archive->name;

        //Panel
        \Kirki::add_section($this->sectionId, array(
            'title'         => $archive->label,
            'panel'         => $panelID,
            'priority'      => 160,
            'preview_url'   => get_post_type_archive_link($archive->name)
        ));

        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'     => 'text',
            'settings' => 'archive_' . $archive->name . '_heading',
            'label'    => esc_html__('Archive heading', 'municipio'),
            'section'  => $this->sectionId,
            'default'  => '',
            'output' => [
                [
                    'type' => 'controller',
                    'as_object' => true,
                ]
            ],
        ]);

        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'     => 'textarea',
            'settings' => 'archive_' . $archive->name . '_body',
            'label'    => esc_html__('Archive lead text', 'municipio'),
            'section'  => $this->sectionId,
            'default'  => '',
            'output' => [
                [
                    'type' => 'controller',
                    'as_object' => true,
                ]
            ],
        ]);

        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'     => 'select',
            'settings' => 'archive_' . $archive->name . '_style',
            'label'    => esc_html__('Style', 'municipio'),
            'section'  => $this->sectionId,
            'default'  => 'cards',
            'choices'  => [
                'compressed' => esc_html__('Compressed', 'municipio'),
                'cards' => esc_html__('Cards', 'municipio'),
                'newsitem' => esc_html__('News', 'municipio'),
                'list' => esc_html__('List', 'municipio'),
                'grid' => esc_html__('Blocks', 'municipio')
            ],
            'output' => [
                [
                    'type' => 'controller',
                    'as_object' => true,
                ]
            ],
        ]);

        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'slider',
            'settings'    => 'archive_' . $archive->name . '_post_count',
            'label'       => esc_html__('Number of posts to display', 'municipio'),
            'description' => esc_html__('How many posts that should be displayed on each page.', 'municipio'),
            'section'     => $this->sectionId,
            'transport'   => 'refresh',
            'default'     => 9,
            'choices'     => [
                'min'  => 4,
                'max'  => 40,
                'step' => 1,
            ]
        ]);

        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'slider',
            'settings'    => 'archive_' . $archive->name . '_number_of_columns',
            'label'       => esc_html__('Number of columns to display', 'municipio'),
            'description' => esc_html__('How many columns that the posts should be divided in.', 'municipio'),
            'section'     => $this->sectionId,
            'transport'   => 'auto',
            'default'     => 4,
            'choices'     => [
                'min'  => 1,
                'max'  => 4,
                'step' => 1,
            ],
            'output' => [
                [
                    'type' => 'controller',
                    'as_object' => true,
                ]
            ],
        ]);

        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'slider',
            'settings'    => 'archive_' . $archive->name . '_number_of_columns',
            'label'       => esc_html__('Number of columns to display', 'municipio'),
            'description' => esc_html__('How many columns that the posts should be divided in.', 'municipio'),
            'section'     => $this->sectionId,
            'transport'   => 'auto',
            'default'     => 4,
            'choices'     => [
                'min'  => 1,
                'max'  => 4,
                'step' => 1,
            ],
            'output' => [
                [
                    'type' => 'controller',
                    'as_object' => true,
                ]
            ],
        ]);

        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'select',
            'settings'    => 'archive_' . $archive->name . '_enabled_filters',
            'label'       => esc_html__('Filters', 'municipio'),
            'description' => esc_html__('Add filters to let the user browse partial content.', 'municipio'),
            'multiple'    => 6,
            'section'     => $this->sectionId,
            'choices'     => array_merge(
                [
                    'text_search' => esc_html__('Text search', 'municipio'),
                    'date_range' => esc_html__('Date range', 'municipio'),
                ],
                (array) $archive->taxonomies
            ),
            'output' => [
                [
                    'type' => 'controller',
                    'as_object' => true,
                ]
            ],
        ]);

        if (!empty($archive->taxonomies)) {
            \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
                'type'        => 'select',
                'settings'    => 'archive_' . $archive->name . '_taxonomies_to_display',
                'label'       => esc_html__('Taxonomy display', 'municipio'),
                'description' => esc_html__('What taxonomies should be displayed?', 'municipio'),
                'multiple'    => 4,
                'section'     => $this->sectionId,
                'choices'     => $archive->taxonomies,
                'output' => [
                    [
                        'type' => 'controller',
                        'as_object' => true,
                    ]
                ],
            ]);
        }

        if (!empty($archive->orderBy)) {
            \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
                'type'        => 'select',
                'settings'    => 'archive_' . $archive->name . '_order_by',
                'label'       => esc_html__('Order By', 'municipio'),
                'description' => esc_html__('Select a key/value to order by.', 'municipio'),
                'section'     => $this->sectionId,
                'choices'     => $archive->orderBy,
                'default'     => 'post_date',
                'output' => [
                    [
                        'type' => 'controller',
                        'as_object' => true,
                    ]
                ],
            ]);

            \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
                'type'        => 'select',
                'settings'    => 'archive_' . $archive->name . '_order_direction',
                'label'       => esc_html__('Order direction', 'municipio'),
                'description' => esc_html__('Select decending or ascending order.', 'municipio'),
                'section'     => $this->sectionId,
                'default'     => 'desc',
                'choices'     => [
                    'asc' => __("Ascending", 'municipio'),
                    'desc' => __("Decending", 'municipio')
                ],
                'output' => [
                    [
                        'type' => 'controller',
                        'as_object' => true,
                    ]
                ],
            ]);
        }
    }
}
