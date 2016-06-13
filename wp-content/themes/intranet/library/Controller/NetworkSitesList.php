<?php

namespace Intranet\Controller;

class NetworkSitesList extends \Municipio\Controller\BaseController
{
    public function init()
    {
        // This will make a variable with name "exampleVariable"
        // accessible from the view of this controller
        $this->data['exampleVariable'] = 'example value';
    }
}
