<?php

namespace Municipio\Search;

class Google
{
    private $apiKey = null;
    private $apiCx = null;

    public $keyword = null;
    public $results = null;

    public $currentPage = 1;
    public $currentIndex = 1;
    public $resultsPerPage = null;

    public function __construct($keyword, $startingIndex = 1)
    {
        $this->apiKey = get_field('google_search_api_key', 'option');
        $this->apiCx = get_field('google_search_api_secret', 'option');

        $this->keyword = $keyword;
        $this->results = $this->search($this->keyword, $startingIndex);
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
        if (!isset($item->pagemap)) {
            return null;
        }

        $meta = $item->pagemap->metatags[0];
        $dateMod = null;

        if (isset($meta->moddate)) {
            $dateMod = $meta->moddate;
        } elseif (isset($meta->pubdate)) {
            $dateMod = $meta->pubdate;
        } elseif (isset($meta->{last-modified})) {
            $dateMod = $meta->{last-modified};
        }

        $dateMod = $this->convertDate($dateMod);

        return $dateMod;
    }

    public function convertDate($date)
    {
        if (substr($date, 0, 1) == 'D') {
            $date = date('d M Y', strtotime(substr($date, 2, -7)));
        } elseif (strlen($date) > 20) {
            $date = date('d M Y', strtotime($date));
        }

        return $date;
    }

    /**
     * Reders the pagination for the current search
     * @param  boolean $echo Wheater to echo the pagination or return as string
     * @return string        The pagination html markup
     */
    public function pagination($echo = false)
    {
        $markup = array();

        // Get needed data
        $results = $this->results;
        $query = $results->queries;

        // Get results per page count
        $this->resultsPerPage = 10;

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

        // How many pages to show in the pager (excluding the current page)
        $numPagesToShow = 10;

        // Get pages
        if ($this->resultsPerPage < $this->results->searchInformation->totalResults) {
            // Calculate number of pages
            $numPages = ceil($this->results->searchInformation->totalResults / $this->resultsPerPage);

            // Calculate range of pages to show in pager
            $startingPage = $this->currentPage - ($numPagesToShow/2);
            $endingPage = $this->currentPage + ($numPagesToShow/2) + 1;

            if ($startingPage < 1) {
                $startingPage = 1;
                $endingPage = $numPagesToShow+2;
            }

            if ($endingPage >= $numPages) {
                $endingPage = $numPages;
            }

            if ($this->currentPage == $endingPage) {
                $endingPage = $numPages+1;
            }

            // Output pages
            for ($i = $startingPage; $i < $endingPage; $i++) {
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
        $nextPage = null;
        if (isset($query->nextPage)) {
            $nextPage = $query->nextPage[0];
            $markup[] = '<li><a class="next" href="?s=' . urlencode(stripslashes($this->keyword)) .
                        '&amp;index=' . $nextPage->startIndex . '">Nästa &raquo;</a></li>';
        }

        $markup[] = '</ul>';

        $markup = implode('', $markup);

        if ($echo === true) {
            echo $markup;
        } else {
            return $markup;
        }
    }
}
