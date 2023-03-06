<?php

namespace Municipio\Customizer\Sections;

class Navigation
{
    private function getScopes(): array
    {
        return [
            'sidebar' => (object) [
                'label' => esc_html__('Sidebar navigation', 'municipio'),
                'scopeClass' => '.s-nav-sidebar',
                'types' => [
                    'vertical'
                ]
            ],
            'drawer' => (object) [
                'label' => esc_html__('Drawer navigation', 'municipio'),
                'scopeClass' => '.s-nav-drawer',
                'types' => [
                    'vertical'
                ]
            ],
            'primary' => (object) [
                'label' => esc_html__('Primary navigation', 'municipio'),
                'scopeClass' => '.s-nav-primary',
                'types' => [
                    'vertical',
                    'horizontal'
                ]
            ]
        ];
    }

    public function __construct(string $sectionID)
    {

        $scopes = $this->getScopes();

        if(is_countable($scopes)) {
            foreach($scopes as $key => $scope) {

                if(in_array('vertical', $scope->types)) {
                    $this->addVerticalColorConfiguration($scope, $sectionID); 
                }

                if(in_array('horizontal', $scope->types)) {
                    $this->addHorizontalColorConfiguration($scope, $sectionID); 
                }

            }
        }
    }


    private function addVerticalColorConfiguration($scope, $sectionID) {
        \Kirki::add_field(
            \Municipio\Customizer::KIRKI_CONFIG,
            [
                'type'        => 'multicolor',
                'settings'    => 'nav_color_' . $key,
                'label'       => $scope->label . " " . esc_html__('colors', 'municipio'),
                'section'     => $sectionID,
                'priority'    => 10,
                'transport'   => 'auto',
                'choices'     => [
                    'divider'    => esc_html__('Divider', 'municipio'),
                    'outline'    => esc_html__('Outline', 'municipio'),
                ],
                'default'     => [
                    'divider'    => 'rgba(0,0,0,0)',
                    'outline'    => 'rgba(0,0,0,0)',
                ],
                'output' => [
                    [
                        'choice'    => 'divider',
                        'element'   => $scope->scopeClass,
                        'property'  => '--color-border-divider',
                    ],
                    [
                        'choice'    => 'outline',
                        'element'   => $scope->scopeClass,
                        'property'  => '--color-border-outline',
                    ],
                ]
            ]
        );
    }

    private function addHorizontalColorConfiguration($scope, $sectionID) {

    }
}
