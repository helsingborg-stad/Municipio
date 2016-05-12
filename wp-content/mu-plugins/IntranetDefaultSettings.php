<?php

namespace IntranetDefaultSettings;

class IntranetDefaultSettings
{
    public function __construct()
    {
        add_action('wpmu_new_blog', array($this, 'setHeaderLayout'));
    }

    public function setHeaderLayout($blogId)
    {
        update_blog_option($blogId, 'options_header_layout', 'intranet');
    }
}

new \IntranetDefaultSettings\IntranetDefaultSettings();

