<?php

namespace Municipio\Controller;

class Search extends \Municipio\Controller\BaseController
{
    public function init()
    {

        //Translations
        $this->data['translation'] = array(
            'filter_results' => __("Filter searchresults", 'municipio'),
            'all_pages' => __("All pages", 'municipio'),
        );

        //Determine what type of searchengine that should be used
        if (get_field('use_google_search', 'option') === true) {
            $this->googleSearch();
        } elseif (get_field('use_algolia_search', 'option') === true) {

            if(function_exists('queryAlgoliaSearch')) {
                $this->algoliaCustomSearch();
            } else {
                $this->algoliaSearch();
            }
        } else {
            $this->wpSearch();
        }

        $this->data['template'] = is_null(get_field('search_result_layout', 'option')) ? 'default' : get_field('search_result_layout', 'option');
        $this->data['gridSize'] = get_field('search_result_grid_columns', 'option');
    }

    /**
     * Default wordpress search
     * @return void
     */
    public function wpSearch()
    {
        global $wp_query;
        $this->data['resultCount'] = $wp_query->found_posts;
        $this->data['keyword'] = get_search_query();
    }

    /**
     * Algolia search
     * @return void
     */
    public function algoliaSearch()
    {
        //Disable results when instant search is on
        if (get_option('algolia_override_native_search') == "instantsearch") {
            $this->data['results'] = array();
            $this->data['resultCount'] = "";
            $this->data['keyword'] = get_search_query();
            return;
        }

        //Mimic wp-search
        global $wp_query;
        $this->data['resultCount'] = $wp_query->found_posts;
        $this->data['keyword'] = get_search_query();
    }

    /**
     * Algolia custom search
     * @return void
     */
    public function algoliaCustomSearch()
    {
        $this->data['results'] = queryAlgoliaSearch(get_search_query());
        $this->data['resultCount'] = count($this->data['results']);
        $this->data['keyword'] = get_search_query();
    }

    /**
     * Google Site Search init
     * @return void
     */
    public function googleSearch()
    {
        $search = new \Municipio\Search\Google(get_search_query(), $this->getIndex());
        $this->data['search'] = $search;
        $this->data['results'] = $search->results;
    }

    /**
     * Get pagination index (used for Google Site Search)
     * @return integer
     */
    public function getIndex()
    {
        return isset($_GET['index']) && is_numeric($_GET['index']) ? sanitize_text_field($_GET['index']) : 1;
    }
}
