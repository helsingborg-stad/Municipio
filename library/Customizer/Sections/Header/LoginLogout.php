<?php

namespace Municipio\Customizer\Sections\Header;

use Municipio\Customizer\KirkiField;
use Municipio\Helper\KirkiSwatches;

class LoginLogout
{
    public function __construct(string $sectionID)
    {
        $colorPalette = KirkiSwatches::getColors();
        KirkiField::addField([
            'type'        => 'select',
            'settings'    => 'header_login_logout_display',
            'label'       => esc_html__('Display login/logout', 'municipio'),
            'description' => esc_html__('Select when the login/logout button should be visible', 'municipio'),
            'section'     => $sectionID,
            'default'     => '',
            'priority'    => 4,
            'choices'     => [
                ''       => esc_html__('Never', 'municipio'),
                'logout' => esc_html__('Logout', 'municipio'),
                'both'   => esc_html__('Login and Logout', 'municipio'),
            ],
            'output'      => [
                ['type' => 'controller']
            ],
        ]);

        KirkiField::addField([
            'type'        => 'checkbox',
            'settings'    => 'header_login_logout_show_in_mobile_menu',
            'label'       => esc_html__('Show in mobile menu', 'municipio'),
            'section'     => $sectionID,
            'default'     => false,
            'priority'    => 10,
            'output'      => [
                [
                    'type'    => 'controller',
                ],
            ],
        ]);

        KirkiField::addField([
            'type'        => 'radio',
            'settings'    => 'login_logout_appearance_type',
            'label'       => esc_html__('Appearance', 'municipio'),
            'description' => esc_html__('Select if you want to use one of the predefined appearance, or customize freely.', 'municipio'),
            'section'     => $sectionID,
            'default'     => 'default',
            'priority'    => 5,
            'choices'     => [
                'default' => esc_html__('Predefined appearance', 'municipio'),
                'custom'  => esc_html__('Custom appearance', 'municipio'),
            ],
        ]);

        KirkiField::addField([
            'type'            => 'select',
            'settings'        => 'login_logout_color_scheme',
            'label'           => esc_html__('Color scheme', 'municipio'),
            'section'         => $sectionID,
            'default'         => 'dark',
            'priority'        => 10,
            'choices'         => [
                'light' => esc_html__('Light', 'municipio'),
                'dark'  => esc_html__('Dark', 'municipio'),
            ],
            'output'          => [
                [
                  'type' => 'controller'
                ]
            ],
            'active_callback' => [
              [
                'setting'  => 'login_logout_appearance_type',
                'operator' => '===',
                'value'    => 'default',
              ]
            ],
          ]);

        KirkiField::addField([
            'type'            => 'multicolor',
            'settings'        => 'header_login_logout_colors_active',
            'label'           => esc_html__('Custom colors user colors', 'municipio'),
            'section'         => $sectionID,
            'priority'        => 10,
            'transport'       => 'auto',
            'choices'         => [
                'user-active-text-color'       => esc_html__('Logout link color', 'municipio'),
                'user-active-text-color-hover' => esc_html__('Logout link color hover', 'municipio'),
                'user-active-author-color'     => esc_html__('User name color', 'municipio'),
            ],
            'default'         => [
                'user-active-text-color'       => '#000',
                'user-active-text-color-hover' => '#000',
                'user-active-author-color'     => '#000',
            ],
            'palettes'        => $colorPalette,
            'output'          => [
                [
                    'choice'   => 'user-active-text-color',
                    'element'  => '.user',
                    'property' => '--user-active-text-color'
                ],
                [
                    'choice'   => 'user-active-text-color-hover',
                    'element'  => '.user',
                    'property' => '--user-active-text-color-hover'
                ],
                [
                    'choice'   => 'user-active-author-color',
                    'element'  => '.user',
                    'property' => '--user-active-author-color'
                ],
            ],
            'active_callback' => [
                [
                  'setting'  => 'login_logout_appearance_type',
                  'operator' => '===',
                  'value'    => 'custom',
                ]
            ],
        ]);

        KirkiField::addField([
            'type'            => 'color',
            'settings'        => 'header_login_logout_background_color',
            'label'           => esc_html__('Custom background color', 'municipio'),
            'section'         => $sectionID,
            'priority'        => 10,
            'default'         => '#ffffff00',
            'choices'         => [
                'alpha' => true,
            ],
            'palettes'        => $colorPalette,
            'output'          => [
                [
                    'type' => 'controller'
                ],
                [
                    'element'  => '.user.user--has-background',
                    'property' => '--user-background-color'
                ]
            ],
            'active_callback' => [
                [
                  'setting'  => 'login_logout_appearance_type',
                  'operator' => '===',
                  'value'    => 'custom',
                ]
            ],
        ]);
    }
}
