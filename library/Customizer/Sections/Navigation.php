<?php

namespace Municipio\Customizer\Sections;

class Navigation
{
    private function getScopes(): array
    {
        return [
            'sidebar' => (object) [
                'label' => esc_html__('Sidebar', 'municipio'),
                'scopeClass' => 's-sidebar'
            ],
            'drawer' => (object) [
                'label' => esc_html__('Sidebar', 'municipio'),
                'scopeClass' => 's-drawer'
            ],
            'primary' => (object) [
                'label' => esc_html__('Primary', 'municipio'),
                'scopeClass' => 's-primary'
            ]
        ];
    }

    public function __construct(string $sectionID)
    {

        $scopes = $this->getScopes();

        if(is_countable($scopes)) {
            foreach($scopes as $key => $scope) {

                \Kirki::add_field(
                    \Municipio\Customizer::KIRKI_CONFIG,
                    [
                        'type'        => 'multicolor',
                        'settings'    => 'nav_color_' . $key,
                        'label'       => $scope->label . " " . esc_html__('colors', 'municipio'),
                        'section'     => $sectionID,
                        'priority'    => 10,
                            'transport' => 'auto',
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
        }
    }
}
