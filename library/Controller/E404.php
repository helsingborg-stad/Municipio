<?php

namespace Municipio\Controller;

class E404 extends \Municipio\Controller\BaseController
{
    public function init()
    {
        $searchKeyword = $_SERVER['REQUEST_URI'];
        $searchKeyword = str_replace('/', ' ', $searchKeyword);
        $searchKeyword = trim($searchKeyword);

        $this->data['keyword'] = $searchKeyword;
    }
}
