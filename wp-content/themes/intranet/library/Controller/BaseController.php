<?php

namespace Intranet\Controller;

class BaseController extends \Municipio\Controller\BaseController
{
    public function __construct()
    {
        $this->missingUserData();

        parent::__construct();
    }

    public function missingUserData()
    {
        $this->data['missing'] = array_merge(
            \Intranet\User\Data::missingRequiredUserData(),
            \Intranet\User\Data::missingRequiredFields()
        );

        $this->data['show_userdata_guide'] = false;
        if (!empty($this->data['missing'])) {
            $this->data['show_userdata_guide'] = true;
        }
    }
}
