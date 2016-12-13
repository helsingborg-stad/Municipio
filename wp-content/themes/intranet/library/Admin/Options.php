<?php

namespace Intranet\Admin;

class Options
{
    public function __construct()
    {
        add_action('admin_init', array($this, 'setupOptions'));
        add_filter('BetterPostUi/authors', function ($args) {
            return array();
        });

        // Nested Pages plugin page author dropdowns
        add_filter('wp_dropdown_users_args', function ($args, $r) {
            if ($r['id'] == 'post_author') {
                $args['who'] = '';
            }

            return $args;
        }, 10, 2);

        add_action('update_option_intranet_force_subscription', function ($option) {
            $cacheKey = md5(serialize(array('getForcedList')));
            wp_cache_delete($cacheKey, self::$cacheGroup);
        });
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

        // Administration unit network
        add_settings_field(
            'intranet_administration_unit_network',
            __('Is administration unit', 'municipio-intranet'),
            function ($args) {
                $html = '<input type="checkbox" id="intranet_administration_unit_network" name="intranet_administration_unit_network" value="true" ' . checked('true', get_option('intranet_administration_unit_network'), false) . '>';
                $html .= '<label for="intranet_administration_unit_network"> '  . $args[0] . '</label>';
                echo $html;
            },
            'general',
            'municipio_intranet',
            array(
                __('Activate to set this site as administration unit (will be displayed in the intranet selector)', 'municipio-intranet')
            )
        );

        register_setting(
            'general',
            'intranet_administration_unit_network'
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

        // AD autosubscribe keys
        add_settings_field(
            'intranet_ad_autosubscribe',
            __('Intranet autosubscribe keys (comma separated)', 'municipio-intranet'),
            function ($args) {
                $html = '<input type="text" class="regular-text ltr" name="intranet_ad_autosubscribe" id="intranet_ad_autosubscribe" value="' . get_option('intranet_ad_autosubscribe') . '">';
                echo $html;
            },
            'general',
            'municipio_intranet'
        );

        register_setting(
            'general',
            'intranet_ad_autosubscribe'
        );

    }
}
