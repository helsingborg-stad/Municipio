<?php

namespace Intranet\Admin;

class Options
{
    public function __construct()
    {
        add_action('admin_init', array($this, 'setupOptions'));
    }

    public function setupOptions()
    {
        add_settings_section(
            'municipio_intranet',
            __('Intranet options', 'municipio-intranet'),
            function () {
                echo '<p>' . __('Intranet specific options.', 'municipio-intranet') . '</p>';
            },
            'general'
        );

        // Intranet hidden
        add_settings_field(
            'intranet_site_hidden',
            __('Hidden', 'municipio-intranet'),
            function ($args) {
                $html = '<input type="checkbox" id="intranet_site_hidden" name="intranet_site_hidden" value="1" ' . checked(1, get_option('intranet_site_hidden'), false) . '>';
                $html .= '<label for="intranet_site_hidden"> '  . $args[0] . '</label>';
                echo $html;
            },
            'general',
            'municipio_intranet',
            array(
                __('Activate to hide this intranet for non administrators and editors', 'municipio-intranet')
            )
        );

        register_setting(
            'general',
            'intranet_site_hidden'
        );

        // Forced subscription
        add_settings_field(
            'intranet_force_subscription',
            __('Force subscription', 'municipio-intranet'),
            function ($args) {
                $html = '<input type="checkbox" id="intranet_force_subscription" name="intranet_force_subscription" value="true" ' . checked('true', get_option('intranet_force_subscription'), false) . '>';
                $html .= '<label for="intranet_force_subscription"> '  . $args[0] . '</label>';
                echo $html;
            },
            'general',
            'municipio_intranet',
            array(
                __('Activate to force all users to subscribe to this intranet', 'municipio-intranet')
            )
        );

        register_setting(
            'general',
            'intranet_force_subscription'
        );

        // Site shortname
        add_settings_field(
            'intranet_short_name',
            __('Intranet short name', 'municipio-intranet'),
            function ($args) {
                $html = '<input type="text" class="regular-text ltr" name="intranet_short_name" id="intranet_short_name" value="' . get_option('intranet_short_name') . '">';
                echo $html;
            },
            'general',
            'municipio_intranet'
        );

        register_setting(
            'general',
            'intranet_short_name'
        );

    }
}
