<?php

namespace Municipio\Controller;

class Search extends \Municipio\Controller\Archive
{

    public function init()
    {
        parent::init();

        //Translations
        $this->data['lang']->allPages               = __("All pages", 'municipio'); 
        $this->data['lang']->noResult               = __("The searchquery did not match any content.", 'municipio'); 
        $this->data['lang']->found                  = __("Found", 'municipio'); 
        $this->data['lang']->results                = __("results", 'municipio'); 
        $this->data['lang']->searchFor              = __("Search for", 'municipio'); 
        $this->data['lang']->viewPage               = __("View page", 'municipio'); 

        //Search general data  
        $this->data['resultCount']                  = $this->wpQuery->found_posts;
        $this->data['keyword']                      = isset($_GET['s']) ? $_GET['s'] : "";

        //Result
        $this->data['posts']                        = $this->getSearchResult($this->wpQuery->posts);

        //Hooks 
        $this->data['hook']->searchNotices          = $this->hook('search_notices'); 
        $this->data['hook']->customSearchPage       = $this->hook('custom_search_page');

        // Show or hide sidebars
        $this->data['showSidebars']                 = false;

    }
    /**
     * Default wordpress search
     * @return object
     */
    private function getSearchResult($posts)
    {
        if(empty($posts)) {
            return []; 
        }

        foreach($posts as $postKey => $post) {
            $posts[$postKey] = \Municipio\Helper\Post::preparePostObject($post);
        }

        return \apply_filters('Municipio/Controller/Search/prepareSearchResultObject', $posts);
    }
}