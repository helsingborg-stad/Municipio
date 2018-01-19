<?php

namespace Municipio\Search;

class Algolia
{
    private $apiKey = null;

    public $keyword = null;
    public $results = null;

    public $currentPage = 1;
    public $currentIndex = 1;
    public $resultsPerPage = null;

    public function __construct($keyword, $startingIndex = 1)
    {
       /* $this->apiKey = get_field('algolia_search_api_key', 'option');
        $this->appId = get_field('algolia_search_app_id', 'option');

        $this->keyword = $keyword;
        $this->results = $this->search($this->keyword, $startingIndex);

        global $wp_query;
        $wp_query->found_posts = $this->results->searchInformation->totalResults;


*/


        //Algolia search modifications
        add_filter('algolia_should_index_post', array($this, 'shouldIndexPost'));
    }


    /**
     * Remove post types from index that are hidden for the user
     * @param $post The post that should be indexed or not
     * @return bool True if add, false if not indexable
     */

    public function shouldIndexPost($post)
    {
        //Get post type object
        if (isset($post->post_type) && $postTypeObject = get_post_type_object($post->post_type)) {

            //Do not index post that are not searchable
            if ($postTypeObject->exclude_from_search) {
                return false;
            }

            //Do not index posts that are not public
            if (!$postTypeObject->publicly_queryable) {
                return false;
            }
        }
        return true;
    }

    /**
     * Perform a search with the Google Custom Search API
     * @param  string  $keyword The search query/keyword
     * @param  integer $start   Starting search result index (used for pagination)
     * @return object           The search results
     */
    public function search($keyword = null, $startingIndex = 1)
    {
        // Handle if keyword is null or empty string
        if ($keyword === null || $keyword === '') {
            return false;
        }

        $url = 'https://www.googleapis.com/customsearch/v1?key=' . $this->apiKey .
               '&cx=' . $this->apiCx .
               '&q=' . urlencode($keyword) .
               '&hl=sv&siteSearchFilter=i&alt=json&start=' . $startingIndex;

        $results = $this->request($url);
        return $results;
    }

    /**
     * Curl the Google API to get the search results
     * @param  string $url The url to curl
     * @return object      The result
     */
    public function request($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        curl_setopt($ch, CURLOPT_REFERER, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        $result = curl_exec($ch);
        curl_close($ch);

        return json_decode($result);
    }

    /**
     * Returns a class to show icon based on filetype if the result item
     * @param  string $filetype The filetype from Google Search API
     * @return string           The class to use to display correct icon
     */
    public function getFiletypeClass($filetype)
    {
        switch ($filetype) {
            case 'PDF/Adobe Acrobat':
                return 'pdf-item';
                break;

            case 'Microsoft Word':
                return 'word-item';
                break;

            default:
                return 'link-item';
                break;
        }
    }

    /**
     * Gets the modified date for an item
     * @param  object $item The item
     * @return string       The modified date
     */
    public function getModifiedDate($item)
    {
        return "";
    }

    public function convertDate($date)
    {
        return date("d M Y", $date);
    }

    /**
     * Reders the pagination for the current search
     * @param  boolean $echo Wheater to echo the pagination or return as string
     * @return string        The pagination html markup
     */
    public function pagination($echo = false)
    {

        /*
        $markup = array();

        // Get needed data
        $results = $this->results;
        $query = $results->queries;

        // Get results per page count
        $this->resultsPerPage = 10;

        if (intval($results->searchInformation->totalResults) <= $this->resultsPerPage) {
            return;
        }

        // Get current page
        $currentPage = 1;
        if (isset($_GET['index']) && $_GET['index'] > 1 && is_numeric(sanitize_text_field($_GET['index']))) {
            $this->currentIndex = sanitize_text_field($_GET['index']);
            $this->currentPage = (($this->currentIndex-1) / $this->resultsPerPage)+1;
        }

        $markup[] = '<ul class="pagination" role="menubar" arial-label="pagination">';

        // Get the previous page
        $previousPage = null;
        if (isset($query->previousPage)) {
            $previousPage = $query->previousPage[0];

            $markup[] = '<li><a class="previous" href="?s=' . urlencode(stripslashes($this->keyword)) .
                        '&amp;index=' . $previousPage->startIndex .
                        '">&laquo; Föregående</a></li>';
        }

        // Get pages
        if ($this->resultsPerPage < $this->results->searchInformation->totalResults) {
            // Calculate number of pages
            // The JSON API returns up to the first 100 results only,
            // see https://developers.google.com/custom-search/json-api/v1/using_rest#search_request_metadata
            $maxResults = 100;
            $numPages = ceil(min($this->results->searchInformation->totalResults, $maxResults) / $this->resultsPerPage);


            // Output pages
            for ($i = 1; $i <= $numPages; $i++) {
                $thisIndex = ($this->resultsPerPage * ($i-1)) + 1;

                $current = null;
                if ($thisIndex == $this->currentIndex) {
                    $current = 'current';
                }

                $markup[] = '<li><a class="page ' . $current . '" href="?s=' . urlencode(stripslashes($this->keyword)) .
                            '&amp;index=' . $thisIndex . '">' . $i . '</a></li>';
            }
        }

        // Get the next page
        if (isset($query->nextPage)) {
            $startIndex = $query->nextPage[0]->startIndex;
            if ($startIndex < $maxResults) {
                $markup[] = '<li><a class="next" href="?s=' . urlencode(stripslashes($this->keyword)) .
              '&amp;index=' . $startIndex . '">Nästa &raquo;</a></li>';
            }
        }

        $markup[] = '</ul>';

        $markup = implode('', $markup);

        if ($echo === true) {
            echo $markup;
            return;
        } else {
            return $markup;
        }*/
    }
}
