<?php

namespace Intranet\Controller;

/**
 * Controllers is loaded based on which theme-file is used on the current
 * page. For instance if the currently used theme-file is Archive.php, the
 * controller loaded would be Archive.
 *
 * Name your controller file Archive.php and the class should be named Archive.
 */

class ExampleController extends \Municipio\Controller\BaseController
{
    public function init()
    {
        // This will make a variable with name "exampleVariable"
        // accessible from the view of this controller
        $this->data['exampleVariable'] = 'example value';
    }
}
