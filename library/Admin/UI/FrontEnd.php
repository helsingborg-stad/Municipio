<?php

namespace Municipio\Admin\UI;

class FrontEnd
{
    public function __construct()
    {
        add_filter('show_admin_bar', '__return_true');
    }
}
