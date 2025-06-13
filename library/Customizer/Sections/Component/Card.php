<?php

namespace Municipio\Customizer\Sections\Component;

use Municipio\Customizer\KirkiField;

class Card
{
    public function __construct(string $sectionID)
    {
        KirkiField::addField([
            'type'     => 'select',
            'settings' => 'component_card_modifier',
            'label'    => esc_html__('Card', 'municipio'),
            'description' => 'Affects every title area wrapped by a card.',
            'section'  => $sectionID,
            'default'  => 'none',
            'priority' => 10,
            'choices'  => [
              'none'      => esc_html__('None', 'municipio'),
              'panel'     => esc_html__('Panel', 'municipio'),
              'accented'  => esc_html__('Accented', 'municipio'),
              'highlight' => esc_html__('Highlight', 'municipio'),
            ],
            'output'   => [
                [
                    'type'    => 'modifier',
                    'context' => [
                        'module.text.box',
                        'module.map',
                        'module.posts.index',
                        'module.posts.list',
                        'module.manual-input.list',
                        'module.posts.expandablelist',
                        'module.manual-input.accordion',
                        'module.inlay.list',
                        'module.index',
                        'module.text.box',
                        'module.files.list',
                        'module.script',
                        'module.localevent.list',
                        'module.contacts.card',
                        'module.contacts.list',
                        'sectionsSplit',
                        'module.sections.split',
                        'module.video'
                    ]
                ]
            ],
        ]);
    }
}
