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
    public function renameDefaultTemplate () {
        return __('Page (default template)', 'municipio');
    }

    /**
     * Register templates
     *
     * @return void
     */
    public function registerTemplates () {
        \Municipio\Helper\Template::add(
            __('Full width', 'municipio'),
            \Municipio\Helper\Template::locateTemplate('full-width.blade.php'),
            'all'
        );

        \Municipio\Helper\Template::add(
            __('One page (no article)', 'municipio'),
            \Municipio\Helper\Template::locateTemplate('one-page.blade.php'),
            'all'
        );

        \Municipio\Helper\Template::add(
            __('Page (two columns)', 'municipio'),
            \Municipio\Helper\Template::locateTemplate('page-two-column.blade.php'),
            'all'
        );

        \Municipio\Helper\Template::add(
            __('Sidebar right', 'municipio'),
            \Municipio\Helper\Template::locateTemplate('sidebar-right.blade.php'),
            'all'
        );
    }
}
