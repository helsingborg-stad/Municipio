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

        global $counts;

        $this->data['keyword'] = get_search_query();
        $this->data['resultCount'] = count($search->results);
        $this->data['counts'] = $counts;
        $this->data['results'] = $search->pageResults;
        $this->data['level'] = $search->level;
    }
}
