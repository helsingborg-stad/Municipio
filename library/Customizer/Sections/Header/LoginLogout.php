<?php

namespace Municipio\Customizer\Sections\Header;

use Municipio\Customizer\CustomizerField;
use Municipio\Helper\ColorSwatches;

class LoginLogout
{
    public function __construct(string $sectionID)
    {
        CustomizerField::addField([
            'type' => 'select',
            'settings' => 'header_login_logout_display',
            'label' => esc_html__('Display login/logout', 'municipio'),
            'description' => esc_html__('Select when the login/logout button should be visible', 'municipio'),
            'section' => $sectionID,
            'default' => '',
            'priority' => 4,
            'choices' => [
                '' => esc_html__('Never', 'municipio'),
                'logout' => esc_html__('Logout', 'municipio'),
                'both' => esc_html__('Login and Logout', 'municipio'),
            ],
            'output' => [
                ['type' => 'controller'],
            ],
        ]);

        CustomizerField::addField([
            'type' => 'checkbox',
            'settings' => 'header_login_logout_show_in_mobile_menu',
            'label' => esc_html__('Show in mobile menu', 'municipio'),
            'section' => $sectionID,
            'default' => false,
            'priority' => 10,
            'output' => [
                [
                    'type' => 'controller',
                ],
            ],
        ]);
    }
}
