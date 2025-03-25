<?php

namespace Municipio\Customizer\Sections\Menu;

use Municipio\Customizer\KirkiField;

class Colors
{
    private function getScopes(): array
    {
        return [
            'sidebar'          => (object) [
                'label'      => esc_html__('Sidebar navigation', 'municipio'),
                'scopeClass' => '.s-nav-sidebar',
                'types'      => [
                    'vertical'
                ],
                'default'    => [
                    'contrasting'         => '#000000',
                    'background_active'   => 'rgba(0,0,0,.04)',
                    'contrasting_active'  => '#000000',
                    'background_expanded' => 'rgba(0,0,0,.04)',
                    'divider_color'       => '#eeeeee'
                ]
            ],
            'drawer'           => (object) [
                'label'      => esc_html__('Drawer navigation', 'municipio'),
                'scopeClass' => '.s-nav-drawer',
                'types'      => [
                    'vertical'
                ],
                'default'    => [
                    'contrasting'         => '#ffffff',
                    'background_active'   => 'rgba(255, 255, 255, 0.04)',
                    'contrasting_active'  => '#ffffff',
                    'background_expanded' => 'rgba(0,0,0,.04)',
                    'divider_color'       => 'rgba(255, 255, 255, 0.05)'
                ]
            ],
            'drawer_secondary' => (object) [
                'label'      => esc_html__('Drawer navigation, secondary', 'municipio'),
                'scopeClass' => '.s-nav-drawer-secondary',
                'types'      => [
                    'vertical'
                ],
                'default'    => [
                    'contrasting'         => '#000000',
                    'background_active'   => 'rgba(0,0,0, 0.05)',
                    'contrasting_active'  => '#000000',
                    'background_expanded' => 'rgba(255,255,255,.04)',
                    'divider_color'       => 'rgba(0,0,0, 0.3)'
                ]
            ],
            'primary'          => (object) [
                'label'      => esc_html__('Primary navigation', 'municipio'),
                'scopeClass' => '.s-nav-primary, .s-header-button',
                'types'      => [
                    'horizontal',
                    'dropdown'
                ],
                'default'    => [
                    'contrasting'        => '#000000',
                    'background_active'  => 'rgba(255, 255, 255, 0)',
                    'contrasting_active' => '#090909'
                ]
            ],
            'language'         => (object) [
                'label'      => esc_html__('Language', 'municipio'),
                'scopeClass' => '.s-nav-language',
                'types'      => [
                    'tiles'
                ],
                'default'    => [
                    'contrasting'        => '#000000',
                    'contrasting_active' => '#000000'
                ]
            ],
            'floating'         => (object) [
                'label'      => esc_html__('Floating', 'municipio'),
                'scopeClass' => '.s-nav-floating',
                'types'      => [
                    'tiles'
                ],
                'default'    => [
                    'contrasting'        => '#000000',
                    'contrasting_active' => '#000000'
                ]
            ],
        ];
    }

    private function getOrientationLabel(string $orientation): string
    {
        if ($orientation == 'horizontal') {
            return " (" . __('Horizontal', 'municipio') . ")";
        }

        if ($orientation == 'vertical') {
            return " (" . __('Vertical', 'municipio') . ")";
        }

        if ($orientation == 'dropdown') {
            return " (" . __('Dropdown', 'municipio') . ")";
        }

        return "";
    }

    public function __construct(string $sectionID)
    {

        $scopes = $this->getScopes();

        if (is_countable($scopes)) {
            foreach ($scopes as $key => $scope) {
                if (in_array('vertical', $scope->types)) {
                    $this->addVerticalColorConfiguration(
                        $key,
                        $scope,
                        $sectionID,
                        $this->getOrientationLabel('vertical')
                    );
                }

                if (in_array('dropdown', $scope->types)) {
                    $this->addVerticalColorConfiguration(
                        $key,
                        $scope,
                        $sectionID,
                        $this->getOrientationLabel('dropdown')
                    );
                }

                if (in_array('horizontal', $scope->types)) {
                    $this->addHorizontalColorConfiguration(
                        $key,
                        $scope,
                        $sectionID,
                        $this->getOrientationLabel('horizontal')
                    );
                }

                if (in_array('tiles', $scope->types)) {
                    $this->addTilesColorConfiguration(
                        $key,
                        $scope,
                        $sectionID,
                        $this->getOrientationLabel('vertical')
                    );
                }
            }
        }
    }

    private function addTilesColorConfiguration($key, $scope, $sectionID, $orientationLabel)
    {
        KirkiField::addField([
            'type'      => 'multicolor',
            'settings'  => 'nav_v_color_' . $key,
            'label'     => $scope->label . " " . esc_html__('colors', 'municipio') . $orientationLabel,
            'section'   => $sectionID,
            'priority'  => 10,
            'transport' => 'auto',
            'alpha'     => true,
            'choices'   => [
                'contrasting'        => esc_html__('Default Contrast', 'municipio'),
                'contrasting_active' => esc_html__('Contrasting (Active)', 'municipio')
            ],
            'default'   => (array) $scope->default,
            'output'    => [
                [
                    'choice'   => 'contrasting',
                    'element'  => $scope->scopeClass,
                    'property' => '--c-nav-v-color-contrasting',
                ],
                [
                    'choice'   => 'contrasting_active',
                    'element'  => $scope->scopeClass,
                    'property' => '--c-nav-v-color-contrasting-active',
                ]
            ]
        ]);
    }

    private function addVerticalColorConfiguration($key, $scope, $sectionID, $orientationLabel)
    {
        KirkiField::addField([
            'type'      => 'multicolor',
            'settings'  => 'nav_v_color_' . $key,
            'label'     => $scope->label . " " . esc_html__('colors', 'municipio') . $orientationLabel,
            'section'   => $sectionID,
            'priority'  => 10,
            'transport' => 'auto',
            'alpha'     => true,
            'choices'   => [
                'contrasting'         => esc_html__('Default Contrast', 'municipio'),
                'background_active'   => esc_html__('Background (Active)', 'municipio'),
                'contrasting_active'  => esc_html__('Contrasting (Active)', 'municipio'),
                'background_expanded' => esc_html__('Background (Expanded)', 'municipio'),
                'divider_color'       => esc_html__('Divider', 'municipio'),
            ],
            'default'   => (array) $scope->default,
            'output'    => [
                [
                    'choice'   => 'contrasting',
                    'element'  => $scope->scopeClass,
                    'property' => '--c-nav-v-color-contrasting',
                ],
                $this->drawerCloseColor($scope->scopeClass),
                [
                    'choice'   => 'background_active',
                    'element'  => $scope->scopeClass,
                    'property' => '--c-nav-v-background-active',
                ],
                [
                    'choice'   => 'contrasting_active',
                    'element'  => $scope->scopeClass,
                    'property' => '--c-nav-v-color-contrasting-active',
                ],
                [
                    'choice'   => 'background_expanded',
                    'element'  => $scope->scopeClass,
                    'property' => '--c-nav-v-background-expanded',
                ],
                [
                    'choice'   => 'divider_color',
                    'element'  => $scope->scopeClass,
                    'property' => '--c-nav-v-divider-color',
                ],
            ]
        ]);
    }

    private function addHorizontalColorConfiguration($key, $scope, $sectionID, $orientationLabel)
    {
        KirkiField::addField([
            'type'      => 'multicolor',
            'settings'  => 'nav_h_color_' . $key,
            'label'     => $scope->label . " " . esc_html__('colors', 'municipio') . $orientationLabel,
            'section'   => $sectionID,
            'priority'  => 10,
            'transport' => 'auto',
            'alpha'     => true,
            'choices'   => [
                'contrasting'        => esc_html__('Default Contrast', 'municipio'),
                'background_active'  => esc_html__('Background (Active)', 'municipio'),
                'contrasting_active' => esc_html__('Contrasting (Active)', 'municipio'),
            ],
            'default'   => (array) $scope->default,
            'output'    => [
                [
                    'choice'   => 'contrasting',
                    'element'  => $scope->scopeClass,
                    'property' => '--c-nav-h-color-contrasting',
                ],
                [
                    'choice'   => 'background_active',
                    'element'  => $scope->scopeClass,
                    'property' => '--c-nav-h-background-active',
                ],
                [
                    'choice'   => 'contrasting_active',
                    'element'  => $scope->scopeClass,
                    'property' => '--c-nav-h-color-contrasting-active',
                ],
            ]
        ]);
    }

    private function drawerCloseColor($scopeClass)
    {
        if ($scopeClass == '.s-nav-drawer') {
            return [
                    'choice'   => 'contrasting',
                    'element'  => '.s-drawer-menu',
                    'property' => '--c-button-default-color',
            ];
        } else {
            return [];
        }
    }
}
