<?php

namespace Municipio\Controller;

class Search extends \Municipio\Controller\BaseController
{
    public function init()
    {
        if (get_field('use_google_search', 'option') === true) {
            $this->googleSearch();
        } else {
            $this->wpSearch();
        }
    }

    public function wpSearch()
    {
        global $wp_query;
        $this->data['resultCount'] = $wp_query->found_posts;
        $this->data['keyword'] = $this->getQuery();
    }

    /**
     * Google Site Search init
     * @return void
     */
    public function googleSearch()
    {
        $search = new \Municipio\Search\Google($this->getQuery(), $this->getIndex());
        $this->data['search'] = $search;
        $this->data['results'] = $search->results;
    }

    /**
     * Get the search keyword from get param
     * @return string Query
     */
    public function getQuery()
    {
        global $s;
        return $s;
    }

    /**
     * Get pagination index (used for Google Site Search)
     * @return integer
     */
    public function getIndex()
    {
        return isset($_GET['index']) && is_numeric($_GET['index']) ? $_GET['index'] : 1;
    }
}
