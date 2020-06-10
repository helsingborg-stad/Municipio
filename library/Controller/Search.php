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

        // Custom null result message
        $this->data['emptySearchResultMessage'] = get_field('empty_search_result_message', 'option');

        //Determine what type of searchengine that should be used
        if (get_field('use_google_search', 'option') === true) {
            $this->googleSearch();
            $this->data['activeSearchEngine'] = "google";
        } elseif (get_field('use_algolia_search', 'option') === true) {
            $displayPostTypes = get_field('algolia_display_post_types', 'option');
            $this->data['displayPostTypes'] = is_array($displayPostTypes) && !empty(array_filter($displayPostTypes)) ? json_encode($displayPostTypes) : json_encode(array());
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

        $this->data['searchResult'] = $this->prepareSearchResultObject();
        $this->data['template'] = is_null(get_field('search_result_layout', 'option')) ? 'default' : get_field('search_result_layout', 'option');
        $this->data['gridSize'] = get_field('search_result_grid_columns', 'option');
        $this->data['pagination'] = $this->preparePaginationObject();
    }

    /**
     * Retrieve search results
     * @return void
     */
    public function wpSearch()
    {
        global $wp_query;
        $this->data['resultCount'] = $wp_query->found_posts;
        $this->data['keyword'] = get_search_query();
    }

    private function preparePaginationObject(){
        global $wp_query;
        $pagination = [];
        $paginationLinks = paginate_links([
                'type' => 'array', 
                'prev_next' => false, 
                'show_all' => true, 
                'current' => $wp_query->max_num_pages + 1
        ]);
       
        for($i = 0; $i < count((array) $paginationLinks); $i++){
            $anchor = new \SimpleXMLElement($paginationLinks[$i]);
            $pagination[] = array(
               'href' => (string) $anchor['href']  . '&pagination=' . (string) ($i + 1),
               'label' => (string) $i + 1
            );
        }

        return \apply_filters('Municipio/Controller/Search/prepareSearchResultObject', $pagination); 
    }

    /**
     * Default wordpress search
     * @return object
     */
    public function prepareSearchResultObject()
    {
        global $wp_query;
        $posts = $wp_query->posts;
        $searchResult = [];
    
        foreach($posts as $post){
            
            $searchResult[] = array(
                'author' => get_the_author_meta( 'display_name', $post->post_author ),
                'date' =>  $post->post_date,
                'title' => $post->post_title,
                'permalink' => get_permalink( $post->ID),
                'excerpt' => wp_trim_words($post->post_content),
                'featuredImage' => get_the_post_thumbnail_url($post->ID),
                'postParent' => $this->getParentPost($post->ID),
                'topMostPostParent' => $this->getTopMostParentPost($post)
            );
        }
        return \apply_filters('Municipio/Controller/Search/prepareSearchResultObject', $searchResult);
        
    }
    
    private function getParentPost($postID) 
    {
        $parentPostID = wp_get_post_parent_id( $postID );
        $parentPost = get_post($parentPostID);

        return $parentPost;
    }
    
    private function getTopMostParentPost($post){
        
            $parents = get_post_ancestors( $post->ID );
            $parentID = end ( $parents );
            $parent = get_post($parentID);
            $parent->href = get_permalink($parentID);

            return apply_filters( "Municipio/Controller/Search/getTopMostParentPost", $parent);
        
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
