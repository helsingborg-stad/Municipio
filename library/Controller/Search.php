<?php

namespace Municipio\Controller;

class Search extends \Municipio\Controller\BaseController
{
    public function init()
    {
        $search = new \Municipio\Search\Google($this->getQuery(), $this->getIndex());
        $this->data['search'] = $search;
        $this->data['results'] = $search->results;
    }

    public function getQuery()
    {
        return isset($_GET['s']) ? $_GET['s'] : null;
    }

    public function getIndex()
    {
        return isset($_GET['index']) && is_numeric($_GET['index']) ? $_GET['index'] : 1;
    }
}
