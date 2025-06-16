<?php

namespace Municipio\Customizer\Sections;

use Municipio\Customizer\KirkiField;

class ErrorPages
{
    public function __construct(string $sectionID, string $type)
    {
        KirkiField::addField([
            'type'     => 'text',
            'settings' => 'error' . $type . '_heading',
            'label'    => esc_html__('Page heading', 'municipio'),
            'section'  => $sectionID,
            'default'  => self::getDefaultHeading($type),
            'output'   => [
                [
                    'type'      => 'controller',
                ]
            ],
        ]);

        KirkiField::addField([
            'type'     => 'textarea',
            'settings' => 'error' . $type . '_description',
            'label'    => esc_html__('Description', 'municipio'),
            'section'  => $sectionID,
            'default'  => self::getDefaultDescription($type),
            'output'   => [
                [
                    'type'      => 'controller',
                ]
            ],
        ]);

        KirkiField::addField([
            'type'        => 'image',
            'settings'    => 'error' . $type . '_image',
            'label'       => esc_html__('Image', 'municipio'),
            'description' => esc_html__('Displays above the heading', 'municipio'),
            'section'     => $sectionID,
            'default'     => '',
            'output'      => [
                [
                    'type'      => 'controller',
                ]
            ],
        ]);

        KirkiField::addField([
            'type'        => 'multicheck',
            'settings'    => 'error' . $type . '_buttons',
            'label'       => esc_html__('Buttons', 'municipio'),
            'description' => esc_html__('What actions button to display', 'municipio'),
            'section'     => $sectionID,
            'choices'     => self::getButtonChoices($type),
            'default'     => array_keys(self::getButtonChoices($type)),
            'output'      => [
                [
                    'type'      => 'controller',
                ]
            ],
        ]);

        KirkiField::addField([
            'type'        => 'switch',
            'settings'    => 'error' . $type . '_backdrop',
            'label'       => esc_html__('Error code backdrop', 'municipio'),
            'description' => esc_html__('Display a faint backdrop with error code ' . $type, 'municipio'),
            'section'     => $sectionID,
            'default'     => 1,
            'choices'     => [
                1 => esc_html__('Show', 'municipio'),
                0 => esc_html__('Hide', 'municipio'),
            ],
            'output'      => [
                [
                    'type'      => 'controller',
                ]
            ],
        ]);
    }

    public static function getDefaultHeading(string $type): string
    {
        switch ($type) {
            case '401':
                return esc_html__("This post is password protected, please log in to view this post.", 'municipio');
            case '403':
                return esc_html__("Your user group do not have access to view this post.", 'municipio');
            case '404':
                return esc_html__("Oops! The page you requested cannot be found.", 'municipio');
            default:
                return '';
        }
    }

    public static function getDefaultDescription(string $type): string
    {
        switch ($type) {
            case '404':
                return esc_html__("The %s you are looking for is either moved or removed.", 'municipio');
            default:
                return '';
        }
    }

    public static function getButtonChoices(string $type): array
    {
        $buttons = [
            'return'  => esc_html__('Previous page', 'municipio'),
            'home'    => esc_html__('To front page', 'municipio'),
            'login'   => esc_html__('Login button', 'municipio'),
        ];

        if (in_array($type, ['403', '404'])) {
            unset($buttons['login']);
        }

        return $buttons;
    }
}
