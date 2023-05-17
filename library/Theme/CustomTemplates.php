<?php

namespace Municipio\Theme;

class CustomTemplates
{
    public function __construct()
    {
        add_action('default_page_template_title', array($this, 'renameDefaultTemplate'));
        add_action('init', array($this, 'registerTemplates'));
    }

    /**
     * Change name on the default template
     *
     * @return string
     */
    public function renameDefaultTemplate()
    {
        return __('Page (default template)', 'municipio');
    }

    /**
     * Register templates
     *
     * @return void
     */
    public function registerTemplates()
    {
        \Municipio\Helper\Template::add(
            __('One Page', 'municipio'),
            \Municipio\Helper\Template::locateTemplate('one-page.blade.php'),
            'all'
        );
        \Municipio\Helper\Template::add(
            __('Page (centered)', 'municipio'),
            \Municipio\Helper\Template::locateTemplate('page-centered.blade.php'),
            'all'
        );
    }
}
