<?php

namespace Intranet\Controller;

class Search extends \Intranet\Controller\BaseController
{
    /**
     * Performs the search
     * @return void
     */
    public function init()
    {
        $search = new \Intranet\SearchWp\Search();

        $this->data['keyword'] = get_search_query();
        $this->data['resultCount'] = count($search->results);
        $this->data['results'] = $search->pageResults;
    }
}
