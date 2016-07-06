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
        if (!is_user_logged_in()) {
            return;
        }

        $this->data['missing'] = array_merge(
            \Intranet\User\Data::missingRequiredUserData(),
            \Intranet\User\Data::missingRequiredFields()
        );

        $this->data['show_userdata_guide'] = false;
        if (!empty($this->data['missing'])) {
            $this->data['show_userdata_guide'] = true;
        }

        $this->data['missing'] = array_merge(\Intranet\User\Data::missingSuggestedFields());

        $this->data['show_userdata_notice'] = false;
        if (!empty($this->data['missing']) && !$this->data['show_userdata_guide']) {
            $this->data['show_userdata_notice'] = true;
        }
    }
}
