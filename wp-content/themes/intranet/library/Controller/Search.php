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
        $this->data['level'] = \Intranet\Search\ElasticSearch::$level;

        $this->data['users'] = \Intranet\User\General::searchUsers(get_search_query());

        if ($this->data['level'] === 'users') {
            $this->data['resultCount'] = count($this->data['users']);
        }

        $this->countResult($this->data['level']);
    }

    /**
     * Counts results for each tab
     * @param  string $currentLevel The current search level
     * @return void
     */
    public function countResult($currentLevel)
    {
        global $wp_query;

        $counts = array(
            'all' => 0,
            'subscriptions' => 0,
            'current' => 0,
        );

        $counts[$currentLevel] = $wp_query->found_posts;

        foreach ($counts as $level => $resultCount) {
            if ($resultCount <> 0) {
                continue;
            }

            $queryVars = $wp_query->query_vars;
            $sites = \Intranet\Search\ElasticSearch::getSitesFromLevel($level);

            $query = new \WP_Query(array(
                's' => get_search_query(),
                'sites' => $sites,
            ));

            $counts[$level] = $query->found_posts;
        }

        $counts['users'] = isset($this->data['users']) ? count($this->data['users']) : 0;

        $this->data['counts'] = $counts;
    }
}
