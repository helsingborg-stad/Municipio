<?php

declare(strict_types=1);

namespace Municipio\Customizer\Sections\Menu;

use Municipio\Customizer\CustomizerField;

class Tabmenu
{
    public function __construct(string $sectionID)
    {
        CustomizerField::addField([
            'type' => 'select',
            'settings' => 'tabmenu_button_color',
            'label' => esc_html__('Tabmenu - Color', 'municipio'),
            'section' => $sectionID,
            'default' => 'inherit',
            'priority' => 10,
            'choices' => [
                'inherit' => esc_html__('Inherit', 'municipio'),
                'primary' => esc_html__('Primary', 'municipio'),
                'secondary' => esc_html__('Secondary', 'municipio'),
            ],
            'output' => [
                ['type' => 'controller'],
            ],
        ]);

        CustomizerField::addField([
            'type' => 'select',
            'settings' => 'tabmenu_button_type',
            'label' => esc_html__('Tabmenu - Type', 'municipio'),
            'section' => $sectionID,
            'default' => 'basic',
            'priority' => 10,
            'choices' => [
                'basic' => esc_html__('Basic', 'municipio'),
                'outlined' => esc_html__('Outlined', 'municipio'),
                'filled' => esc_html__('Filled', 'municipio'),
            ],
            'output' => [
                ['type' => 'controller'],
            ],
        ]);
    }
}
