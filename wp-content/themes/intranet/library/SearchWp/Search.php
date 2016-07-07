<?php

namespace Intranet\SearchWp;

class Search
{
    public $wpSearchResult = array();
    public $results = array();

    public $currentPage = 1;
    public $currentIndex = 1;
    public $resultsPerPage = null;

    public $level = 'subscriptions';

    public $users = array();
    public $levelResultsCount = array(
        'subscriptions' => null,
        'all' => null,
        'current' => null,
        'users' => null
    );

    public function __construct()
    {
        $this->getLevel();

        $this->users = $this->searchUsers();
        $content = $this->searchWp();

        switch ($this->level) {
            case 'users':
                $this->results = $this->users;
                break;

            case 'ajax':
                $this->results = array(
                    'users' => array_slice($this->users, 0, 5),
                    'content' => array_slice($content, 0, 5)
                );
                break;

            default:
                $this->results = $content;
                break;
        }

        if (defined('DOING_AJAX') && DOING_AJAX) {
            return;
        }

        $this->renderResult();

        add_filter('Municipio/search_result/excerpt', array($this, 'highlightTerms'));
    }

    /**
     * Get the search level
     * @return void
     */
    public function getLevel()
    {
        if (!isset($_REQUEST['level']) || empty($_REQUEST['level'])) {
            return;
        }

        $this->level = sanitize_text_field($_REQUEST['level']);
    }

    /**
     * Render the search result
     * @return void
     */
    public function renderResult()
    {
        $this->setupPagination();

        $offset = 0;
        if ($this->currentPage > 1) {
            $offset = ($this->currentPage-1) * $this->resultsPerPage;
        }

        $this->pageResults = array_slice($this->results, $offset, $this->resultsPerPage);

        global $counts;
        $counts = array(
            'users' => count($this->users)
        );
    }

    /**
     * Search users
     * @return array Users
     */
    public function searchUsers()
    {
        $keyword = get_search_query();
        return \Intranet\User\General::searchUsers($keyword);
    }

    /**
     * Search network wide with Search WP plugin
     * @return void
     */
    public function searchWp()
    {
        $this->resultsPerPage = get_blog_option(BLOG_ID_CURRENT_SITE, 'posts_per_page');
        $this->resultsPerPage = 8;

        if (isset($_REQUEST['page']) && !empty($_REQUEST['page'])) {
            $this->currentPage = sanitize_text_field($_REQUEST['page']);
        }

        // Get results for the other sites
        $this->multisiteSearchWP();
        $this->orderResultsByWeight();
        $results = $this->getPostsFromResult();

        return $results;
    }

    /**
     * Get the post objects
     * @return void
     */
    public function getPostsFromResult()
    {
        $results = array();

        foreach ($this->wpSearchResult as $item) {
            $post = get_blog_post($item['blog_id'], $item['post_id']);
            $results[$item['post_id']] = $post;
            $results[$item['post_id']]->blog_id = $item['blog_id'];
            $results[$item['post_id']]->is_file = false;

            if (!$post->post_mime_type) {
                $results[$item['post_id']]->permalink = get_blog_permalink($item['blog_id'], $item['post_id']);
            } else {
                $results[$item['post_id']]->permalink = esc_url($post->guid);
                $results[$item['post_id']]->is_file = true;
            }
        }

        return $results;
    }

    /**
     * Search each site in the network
     * @return void
     */
    public function multisiteSearchWP()
    {
        global $searchwp;
        $sites = null;

        $level = $this->level;

        switch ($level) {
            case 'current':
                $sites = array(get_current_blog_id());
                break;

            case 'subscriptions':
                $sites = array_merge(
                    \Intranet\User\Subscription::getSubscriptions(get_current_user_id(), true),
                    \Intranet\User\Subscription::getForcedSubscriptions(true)
                );
                break;

            default:
                $sites = \Intranet\Helper\Multisite::getSitesList(true, true);
                break;
        }

        foreach ($sites as $siteId) {
            switch_to_blog($siteId);

            $s = isset($searchwp->original_query) && strlen($searchwp->original_query) > 0 ? $searchwp->original_query : get_search_query();

            $searchwp->search('default', $s);
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

    /**
     * Setup the pagination
     * @return void
     */
    public function setupPagination()
    {
        $markup = array();

        // Get current page
        $currentPage = 1;
        if (isset($_REQUEST['page']) && !empty($_REQUEST['page'])) {
            $currentPage = sanitize_text_field($_REQUEST['page']);
            $this->currentPage = sanitize_text_field($_REQUEST['page']);
        }

        $markup[] = '<ul class="pagination" role="menubar" arial-label="pagination">';

        // Prev page button
        // Get the previous page
        $previousPage = null;
        if ($currentPage > 1) {
            $previousPage = $currentPage -1;
            $prevUrl = \Municipio\Helper\Url::getQueryString();
            $prevUrl['page'] = $previousPage;
            $prevUrl = home_url('?' . http_build_query($prevUrl));

            $markup[] = '<li><a class="prev" href="' . $prevUrl . '">&laquo; ' . __('Previous', 'municipio-intranet') . '</a></li>';
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
                if ($i > $numPages) {
                    continue;
                }

                $current = null;
                if ($this->currentPage == $i) {
                    $current = 'current';
                }

                $pageUrl = \Municipio\Helper\Url::getQueryString();
                $pageUrl['page'] = $i;
                $pageUrl = home_url('?' . http_build_query($pageUrl));

                $markup[] = '<li><a class="page ' . $current . '" href="' . $pageUrl . '">' . $i . '</a></li>';
            }
        }

        // Get the next page
        $nextPage = null;
        if ($this->currentPage < $numPages) {
            $nextPage = $this->currentPage + 1;
            $nextUrl = \Municipio\Helper\Url::getQueryString();
            $nextUrl['page'] = $nextPage;
            $nextUrl = home_url('?' . http_build_query($nextUrl));

            $markup[] = '<li><a class="next" href="' . $nextUrl . '">' . __('Next', 'municipio-intranet') . ' &raquo;</a></li>';
        }


        // End tag for ul
        $markup[] = '</ul>';

        $this->data['pagination'] = implode('', $markup);
    }

    /**
     * Highlight terms
     * @param  string $originalExcerpt Original excerpt
     * @return string                  Highlighted excerpt
     */
    public function highlightTerms($originalExcerpt)
    {
        global $post;

        if (!is_search() || !function_exists('searchwp_term_highlight_get_the_excerpt_global')) {
            return $originalExcerpt;
        }

        // prevent recursion
        remove_filter('get_the_excerpt', array($this, 'highlightTerms'));
        $excerpt = searchwp_term_highlight_get_the_excerpt_global($post->ID, null, get_search_query());
        add_filter('get_the_excerpt', array($this, 'highlightTerms'));

        $excerpt = preg_replace('/<span class=\'searchwp-highlight\'>(.*?)<\/span>/', '<mark class="mark-blue">$1</mark>', $excerpt);

        if (empty($excerpt)) {
            return $originalExcerpt;
        }

        return $excerpt;
    }
}
