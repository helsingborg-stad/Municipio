<?php

namespace Municipio\Admin\Acf;

class LocationRules
{
    public function __construct()
    {
        add_action('acf/init', function () {
            if (function_exists('acf_register_location_type')) {
                include_once('LocationRulesContentType.php');
                acf_register_location_type('LocationRulesContentType');
            }
        });
    }
}
