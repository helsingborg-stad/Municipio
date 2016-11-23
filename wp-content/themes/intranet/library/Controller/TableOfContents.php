<?php

namespace Intranet\Controller;

class TableOfContents extends \Intranet\Controller\BaseController
{
    public function init()
    {
        $site = null;
        $search = null;

        if (isset($_GET['department']) && !empty($_GET['department']) && is_numeric($_GET['department'])) {
            $site = $_GET['department'];
        }

        if (isset($_GET['title']) && !empty($_GET['title'])) {
            $search = $_GET['title'];
        }

        $this->data['tableOfContents'] = \Intranet\Theme\TableOfContents::get($site, $search);

        $this->data['selectedDepartment'] = isset($_GET['department']) && !empty($_GET['department']) ? $_GET['department'] : null;
        $this->data['titleQuery'] = isset($_GET['title']) && !empty($_GET['title']) ? $_GET['title'] : null;
    }
}
