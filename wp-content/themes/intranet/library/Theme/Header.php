<?php

namespace Intranet\Theme;

class Header
{
    public function __construct()
    {
        add_filter('acf/load_field/name=header_layout', array($this, 'addIntranetHeader'));

        add_action('wp_ajax_nopriv_search_sites', '\Intranet\Helper\Multisite::searchSites');
        add_action('wp_ajax_search_sites', '\Intranet\Helper\Multisite::searchSites');
    }

    public function addIntranetHeader($field)
    {
        $field['choices']['intranet'] = 'Intranet';
        return $field;
    }
}
