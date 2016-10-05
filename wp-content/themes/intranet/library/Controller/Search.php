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
        global $wp_query;
        $this->data['resultCount'] = $wp_query->found_posts;
        $this->data['keyword'] = get_search_query();
        $this->data['level'] = \Intranet\Search\Elasticsearch::$level;
        $this->data['counts'] = array(
            'all' => 0,
            'subscriptions' => 0,
            'current' => 0,
            'users' => 0
        );
    }
}
