<?php

namespace Municipio\Customizer\Sections\Header;

use Municipio\Customizer\KirkiField;

class LoginLogout
{
    public function __construct(string $sectionID)
    {
        KirkiField::addField([
            'type'        => 'select',
            'settings'    => 'header_login_logout',
            'label'       => esc_html__('Display login/logout', 'municipio'),
            'description' => esc_html__('Select when the login/logout button should be visible', 'municipio'),
            'section'     => $sectionID,
            'default'     => 'logout',
            'priority'    => 10,
            'choices'     => [
                ''          => esc_html__('Never', 'municipio'),
                'logout'    => esc_html__('Logout', 'municipio'),
                'both'      => esc_html__('Login and Logout', 'municipio'),
            ],
            'output'    => [
                ['type' => 'controller']
            ],
        ]);
    }
}
