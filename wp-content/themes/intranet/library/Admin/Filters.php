<?php

namespace Intranet\Admin;

class Filters
{
    public function __construct()
    {
        add_action('admin_init', array($this, 'removeNewsFromPageForPostTypes'), 11);
    }

    public function removeNewsFromPageForPostTypes()
    {
        global $wp_settings_fields;

        if (isset($wp_settings_fields['reading']['page_for_post_type']['page_for_intranet-news'])) {
            unset($wp_settings_fields['reading']['page_for_post_type']['page_for_intranet-news']);
        }
    }
}
