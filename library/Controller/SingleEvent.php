<?php

namespace Municipio\Controller;

class SingleEvent extends \Municipio\Controller\BaseController
{
    public function init()
    {
        $this->data['occasion'] = method_exists('\EventManagerIntegration\Helper\SingleEventData', 'singleEventDate') ? \EventManagerIntegration\Helper\SingleEventData::singleEventDate() : null;
    }
}
