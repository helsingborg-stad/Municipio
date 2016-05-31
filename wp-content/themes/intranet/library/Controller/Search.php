<?php

namespace Intranet\Controller;

class Search extends \Municipio\Controller\BaseController
{
    public $wpSearchResult = array();
    public $results = array();

    public $currentPage = 1;
    public $currentIndex = 1;
    public $resultsPerPage = null;

    public function init()
    {
        global $searchwp;

        $this->resultsPerPage = get_blog_option(BLOG_ID_CURRENT_SITE, 'posts_per_page');
        $this->resultsPerPage = 8;

        if (isset($_GET['page']) && !empty($_GET['page'])) {
            $this->currentPage = sanitize_text_field($_GET['page']);
        }

        foreach ($searchwp->results_weights as $key => $weight) {
            $searchwp->results_weights[$key]['blog_id'] = get_current_blog_id();
        }

        // Store the results from the current site
        $this->wpSearchResult = array_merge($this->wpSearchResult, $searchwp->results_weights);

        // Get results for the other sites
        $this->multisiteSearchWP();
        $this->orderResultsByWeight();
        $this->getPostsFromResult();
        $this->setupPagination();

        $this->data['keyword'] = get_search_query();
        $this->data['resultCount'] = count($this->results);
        $this->data['results'] = $this->pageResults;
    }

    /**
     * Get the post objects
     * @return void
     */
    public function getPostsFromResult()
    {
        foreach ($this->wpSearchResult as $item) {
            $this->results[$item['post_id']] = get_blog_post($item['blog_id'], $item['post_id']);
            $this->results[$item['post_id']]->blog_id = $item['blog_id'];
        }

        $offset = 0;
        if ($this->currentPage > 1) {
            $offset = ($this->currentPage-1) * $this->resultsPerPage;
        }

        $this->pageResults = array_slice($this->results, $offset, $this->resultsPerPage);
    }

    /**
     * Search each site in the network
     * @return void
     */
    public function multisiteSearchWP()
    {
        global $searchwp;
        $sites = \Intranet\Helper\Multisite::getSitesList(false, true);

        foreach ($sites as $siteId) {
            switch_to_blog($siteId);

            $searchwp->search('default', $searchwp->original_query);
            foreach ($searchwp->results_weights as $key => $weight) {
                $searchwp->results_weights[$key]['blog_id'] = (int)$siteId;
            }

            $this->wpSearchResult = array_merge($this->wpSearchResult, $searchwp->results_weights);

            restore_current_blog();
        }
    }

    /**
     * Order the result by weight
     * @return void
     */
    public function orderResultsByWeight()
    {
        return uasort($this->wpSearchResult, function ($a, $b) {
            return $a['weight'] < $b['weight'];
        });
    }

    public function setupPagination()
    {
        $markup = array();

        // Get current page
        $currentPage = 1;
        if (isset($_GET['page']) && !empty($_GET['page'])) {
            $currentPage = sanitize_text_field($_GET['page']);
            $this->currentPage = sanitize_text_field($_GET['page']);
        }

        $markup[] = '<ul class="pagination" role="menubar" arial-label="pagination">';

        // Prev page button
        // Get the previous page
        $previousPage = null;
        if ($currentPage > 1) {
            $previousPage = $currentPage -1;

            $markup[] = '<li><a class="prev" href="?s=' . get_search_query() .
                        '&amp;page=' . $previousPage . '">&laquo; ' . __('Previous', 'municipio-intranet') . '</a></li>';
        }

        // How many pages to show in the pager (excluding the current page)
        $numPagesToShow = 10;

        // Get pages
        $numPages = 0;
        if ($this->resultsPerPage < count($this->results)) {
            // Calculate number of pages
            $numPages = ceil(count($this->results) / $this->resultsPerPage);

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
            for ($i = $startingPage; $i <= $endingPage; $i++) {
                if ($i > $numPages) continue;

                $current = null;
                if ($this->currentPage == $i) {
                    $current = 'current';
                }

                $markup[] = '<li><a class="page ' . $current . '" href="?s=' . get_search_query() .
                            '&amp;page=' . $i . '">' . $i . '</a></li>';
            }
        }

        // Get the next page
        $nextPage = null;
        if ($this->currentPage < $numPages) {
            $nextPage = $this->currentPage + 1;
            $markup[] = '<li><a class="next" href="?s=' . get_search_query() .
                        '&amp;index=' . $nextPage . '">' . __('Next', 'municipio-intranet') . ' &raquo;</a></li>';
        }


        // End tag for ul
        $markup[] = '</ul>';

        $this->data['pagination'] = implode('', $markup);
    }
}
