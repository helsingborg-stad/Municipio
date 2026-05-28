<?php

namespace Municipio\Customizer\Sections;

use Municipio\Customizer\KirkiField;

class Width
{
    public function __construct($sectionID)
    {
        KirkiField::addField([
            'type' => 'custom',
            'settings' => 'heading_width',
            'section' => $sectionID,
            'default' => '
                                <h2>' . esc_html__('Width of page columns', 'municipio') . ' </h2> 
                                <p class="description customize-section-description">' . esc_html__('Set the width of left & right columns. The middle (content) column will use whatever space left.', 'municipio') . '</p>
                            ',
        ]);

        KirkiField::addField([
            'type' => 'select',
            'settings' => 'column_size_left',
            'label' => esc_html__('Left', 'municipio'),
            'section' => $sectionID,
            'default' => 'normal',
            'priority' => 15,
            'choices' => [
                'normal' => esc_html__('Normal', 'municipio'),
                'large' => esc_html__('Large', 'municipio'),
            ],
            'output' => [
                ['type' => 'controller'],
            ],
        ]);

        KirkiField::addField([
            'type' => 'select',
            'settings' => 'column_size_right',
            'label' => esc_html__('Right', 'municipio'),
            'section' => $sectionID,
            'default' => 'normal',
            'priority' => 20,
            'choices' => [
                'normal' => esc_html__('Normal', 'municipio'),
                'large' => esc_html__('Large', 'municipio'),
            ],
            'output' => [
                ['type' => 'controller'],
            ],
        ]);
    }
}
