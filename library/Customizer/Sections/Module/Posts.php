<?php

namespace Municipio\Customizer\Sections\Module;

class Posts
{
    public function __construct(string $sectionID)
    {
        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
          'type'        => 'select',
          'settings'    => 'mod_posts_index_modifier',
          'label'       => esc_html__('Index', 'municipio'),
          'section'     => $sectionID,
          'default'     => 'none',
          'priority'    => 10,
          'choices'     => [
            'none' => esc_html__('None', 'municipio'),
            'panel' => esc_html__('Panel', 'municipio'),
            'accented' => esc_html__('Accented', 'municipio'),
            'highlight' => esc_html__('Highlight', 'municipio'),
          ],
          'output' => [
              [
                'type' => 'modifier',
                'context' => ['module.posts.index']
              ]
          ],
        ]);

        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
          'type'        => 'select',
          'settings'    => 'mod_posts_list_modifier',
          'label'       => esc_html__('List', 'municipio'),
          'section'     => $sectionID,
          'default'     => 'none',
          'priority'    => 10,
          'choices'     => [
            'none' => esc_html__('None', 'municipio'),
            'panel' => esc_html__('Panel', 'municipio'),
            'accented' => esc_html__('Accented', 'municipio'),
            'highlight' => esc_html__('Highlight', 'municipio'),
          ],
          'output' => [
              [
                'type' => 'modifier',
                'context' => ['module.posts.list']
              ]
          ],
        ]);

        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
          'type'        => 'select',
          'settings'    => 'mod_posts_expandablelist_modifier',
          'label'       => esc_html__('Expandable List', 'municipio'),
          'section'     => $sectionID,
          'default'     => 'none',
          'priority'    => 10,
          'choices'     => [
            'none' => esc_html__('None', 'municipio'),
            'panel' => esc_html__('Panel', 'municipio'),
            'accented' => esc_html__('Accented', 'municipio'),
            'highlight' => esc_html__('Highlight', 'municipio'),
          ],
          'output' => [
              [
                'type' => 'modifier',
                'context' => ['module.posts.expandablelist']
              ]
          ],
        ]);
    }
}
