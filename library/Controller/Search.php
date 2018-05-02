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
            $this->data['activeSearchEngine'] = "google";
        } elseif (get_field('use_algolia_search', 'option') === true) {
            if(function_exists('queryAlgoliaSearch')) {
                $this->algoliaCustomSearch();
                $this->data['activeSearchEngine'] = "algoliacustom";
            } else {
                if(get_option('algolia_override_native_search') == "instantsearch") {
                    $this->algoliaSearchInstant();
                    $this->data['activeSearchEngine'] = "algoliainstant";
                } else {
                    $this->algoliaSearch();
                    $this->data['activeSearchEngine'] = "algolia";
                }
            }
        } else {
            $this->wpSearch();
            $this->data['activeSearchEngine'] = "wp";
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
        //Mimic wp-search
        global $wp_query;
        $this->data['resultCount'] = $wp_query->found_posts;
        $this->data['keyword'] = get_search_query();
    }

    /**
     * Algolia search
     * @return void
     */
    public function algoliaSearchInstant()
    {
        $this->data['results'] = array();
        $this->data['resultCount'] = "";
        $this->data['keyword'] = get_search_query();
    }

    /**
     * Algolia custom search
     * @return void
     */
    public function algoliaCustomSearch()
    {
        $this->data['results'] = queryAlgoliaSearch(get_search_query());
        $this->data['keyword'] = get_search_query();

        //Get count per index
        $this->data['resultIndexCount'] = $this->algoliaCustomSearchResultCount($this->data['results']);

        $this->data['resultIndexCountUrl'] = implode("-", $this->data['resultIndexCount']);

        //Total count
        if(isset($_GET['count_data'])) {
            $this->data['resultCount'] = array_sum(explode("-", $_GET['count_data']));
        } else {
            if(is_array($this->data['results'])) {
                $this->data['resultCount'] = count($this->data['results']);
            } else {
                $this->data['resultCount'] = 0;
            }
        }

        //Pagination
        if(is_array($this->data['results'])) {
            $this->data['paginatedResults'] = array_chunk($this->data['results'], 30);
        } else {
            $this->data['paginatedResults'] = array();
        }

        $this->data['pg'] = isset($_GET['pg']) && is_numeric($_GET['pg']) ? $_GET['pg'] : 0;
    }

    public function algoliaCustomSearchResultCount($result) {

        //Get for url if defined
        if(isset($_GET['count_data'])) {
            return explode("-", $_GET['count_data']);
        }

        //Get from backend
        $return = array();
        if(is_array($result) && !empty($result)) {
            foreach ($result as $item) {
                if (isset($return[$item['index_id']])) {
                    $return[$item['index_id']] = $return[$item['index_id']] +1;
                } else {
                    $return[$item['index_id']] = 1;
                }
            }
        }

        //Sort
        ksort($return);

        //Return
        return $return;
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
