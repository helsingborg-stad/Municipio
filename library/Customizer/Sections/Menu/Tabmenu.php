<?php

namespace Municipio\Customizer\Sections\Menu;

use Municipio\Customizer\KirkiField;

class Tabmenu
{
    public function __construct(string $sectionID)
    {
        KirkiField::addField([
            'type'     => 'select',
            'settings' => 'tabmenu_button_color',
            'label'    => esc_html__('Tabmenu - Color', 'municipio'),
            'section'  => $sectionID,
            'default'  => 'default',
            'priority' => 10,
            'choices'  => [
                'default'   => esc_html__('Default', 'municipio'),
                'primary'   => esc_html__('Primary', 'municipio'),
                'secondary' => esc_html__('Secondary', 'municipio'),
            ],
            'output'   => [
                ['type' => 'controller']
            ],
        ]);

        KirkiField::addField([
            'type'     => 'select',
            'settings' => 'tabmenu_button_type',
            'label'    => esc_html__('Tabmenu - Type', 'municipio'),
            'section'  => $sectionID,
            'default'  => 'filled',
            'priority' => 10,
            'choices'  => [
                'basic'    => esc_html__('Basic', 'municipio'),
                'outlined' => esc_html__('Outlined', 'municipio'),
                'filled'   => esc_html__('Filled', 'municipio'),
            ],
            'output'   => [
                ['type' => 'controller']
            ],
        ]);

        KirkiField::addField([
            'type'     => 'select',
            'settings' => 'header_trigger_button_color',
            'label'    => esc_html__('Trigger button color', 'municipio'),
            'section'  => $sectionID,
            'default'  => 'default',
            'priority' => 10,
            'choices'  => [
                'default'   => esc_html__('Default', 'municipio'),
                'primary'   => esc_html__('Primary', 'municipio'),
                'secondary' => esc_html__('Secondary', 'municipio'),
            ],
            'output'   => [
                ['type' => 'controller']
            ],
        ]);

        KirkiField::addField([
            'type'     => 'select',
            'settings' => 'header_trigger_button_type',
            'label'    => esc_html__('Trigger button type', 'municipio'),
            'section'  => $sectionID,
            'default'  => 'basic',
            'priority' => 10,
            'choices'  => [
                'basic'    => esc_html__('Basic', 'municipio'),
                'outlined' => esc_html__('Outlined', 'municipio'),
                'filled'   => esc_html__('Filled', 'municipio'),
            ],
            'output'   => [
                ['type' => 'controller']
            ],
        ]);

        KirkiField::addField([
            'type'     => 'select',
            'settings' => 'header_trigger_button_size',
            'label'    => esc_html__('Trigger button size', 'municipio'),
            'section'  => $sectionID,
            'default'  => 'md',
            'priority' => 10,
            'choices'  => [
                'sm' => esc_html__('Small', 'municipio'),
                'md' => esc_html__('Medium', 'municipio'),
                'lg' => esc_html__('Large', 'municipio'),
            ],
            'output'   => [
                ['type' => 'controller']
            ],
        ]);
    }
}
