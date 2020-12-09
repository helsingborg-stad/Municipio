<?php

namespace Municipio\Controller;

class Archive extends \Municipio\Controller\BaseController
{
    private static $gridSize;

    private static $randomGridBase = array();
    private static $gridRow = array();
    private static $gridColumns = array();

    public function init()
    {

        //Get current post type
        $postType = !empty($this->data['postType']) ? $this->data['postType'] : 'page';
        $template = $this->getTemplate($postType);

        //Get template
        $this->data['template']                 = $template;

        //The posts
        $this->data['posts']                    = $this->getPosts($template);

        //Sidebar
        $this->data['showSidebarNavigation']    = $this->showSidebarNavigation($postType);

        // Show or hide sidebars
        $this->data['showSidebars']             = true;

        //Set default values to query parameters
        $this->data['queryParameters']          = $this->setQueryParameters();

        //Filter options
        $this->data['taxonomyFilters']          = $this->getTaxonomyFilters($postType);
        $this->data['filterPosition']           = $this->getFilterPosition($postType);
        $this->data['enableTextSearch']         = $this->enableTextSearch($postType);
        $this->data['enableDateFilter']         = $this->enableDateFilter($postType);

        //Archive data
        $this->data['archiveTitle']             = $this->getArchiveTitle($postType);
        $this->data['archiveBaseUrl']           = $this->getPostTypeArchiveLink($postType);
        $this->data['gridColumnClass']          = $this->getGridClass($postType);

        //Pagination
        $this->data['currentPage']              = $this->getCurrentPage();
        $this->data['paginationList']           = $this->getPagination($postType, $this->data['archiveBaseUrl'], $this->wpQuery);
        $this->data['showPagination']           = $this->showPagination($postType, $this->data['archiveBaseUrl'], $this->wpQuery);

        //Display functions
        $this->data['showFilterReset']          = $this->showFilterReset($this->data['queryParameters']);
        $this->data['showDatePickers']          = $this->showDatePickers($this->data['queryParameters']);

        //Show filter?
        $this->data['showFilter']               = $this->showFilter($postType);

        //Language
        if(!isset($this->data['lang'])) {
            $this->data['lang'] = (object) [];
        }
        
        $this->data['lang']->noResult         = $this->data['postTypeDetails']->labels->not_found;
        $this->data['lang']->publish          = __('Published', 'municipio');
        $this->data['lang']->updated          = __('Updated', 'municipio');
        $this->data['lang']->readMore         = __('Read more', 'municipio');
        $this->data['lang']->searchFor        = ucfirst(strtolower($this->data['postTypeDetails']->labels->search_items));

        $this->data['lang']->fromDate         = __('Choose a from date', 'municipio');
        $this->data['lang']->toDate           = __('Choose a to date', 'municipio');
        $this->data['lang']->dateInvalid      = __('Select a valid date', 'municipio');

        $this->data['lang']->searchBtn        = __('Search', 'municipio');
        $this->data['lang']->resetBtn         = __('Reset filter', 'municipio');

        //Filter
        $this->data = apply_filters(
            'Municipio/Controller/Archive/Data',
            $this->data,
            $postType,
            $template
        );

    }

    /**
     * Determines if view for filter should be rendered.
     *
     * @param string $postType
     * @return boolean
     */
    public function showFilter($postType) {
        return (bool) array_filter([
            $this->enableTextSearch($postType),
            $this->enableDateFilter($postType),
            $this->getTaxonomyFilters($postType)
        ]);
    }

    /**
     * Boolean function to determine if navigation should be shown
     *
     * @param   string      $postType   The current post type
     * @return  boolean                 True or false val.
     */
    public function showSidebarNavigation($postType) {
        return (bool) get_field('archive_' . sanitize_title($postType) . '_show_sidebar_navigation', 'option');
    }

    /**
     * Boolean function to determine if text search should be enabled
     *
     * @param   string      $postType   The current post type
     * @return  boolean                 True or false val.
     */
    public function enableTextSearch($postType) {
        return (bool) in_array('text_search', (array) get_field('archive_' . sanitize_title($postType) . '_post_filters_header', 'options'));
    }

    /**
     * Boolean function to determine if date filter should be enabled
     *
     * @param   string      $postType   The current post type
     * @return  boolean                 True or false val.
     */
    public function enableDateFilter($postType) {
        return (bool) in_array('date_range', (array) get_field('archive_' . sanitize_title($postType) . '_post_filters_header', 'options'));
    }

    /**
     * Get the position of the filter
     *
     * @param string $postType
     *
     * @return string
     */
    public function getFilterPosition(string $postType) {
        return (string) get_field('archive_' . sanitize_title($postType) . '_filter_position', 'option');
    }

    /**
     * Get the current page
     *
     * @param   integer $default    Default page if not set
     * @return  integer             The current page
     */
    public function getCurrentPage(int $default = 1) : int
    {
        return (get_query_var('paged')) ? get_query_var('paged') : $default;
    }

    /**
     * Get the template style for this archive
     *
     * @param string $postType  The post type to get the option from
     * @param string $default   The default value, if not found.
     *
     * @return string
     */
    public function getTemplate(string $postType, string $default = 'collapsed') : string
    {
        $archiveOption = get_field('archive_' . sanitize_title($this->data['postType']) . '_post_style', 'option');

        if(!empty($archiveOption)) {
            return $archiveOption;
        }

        return $default;
    }

    /**
     * Get the grid class
     *
     * @param   string  $postType   The current post type
     *
     * @return void
     */
    public function getGridClass(string $postType) {
        $gridSize = get_field('archive_' . sanitize_title($postType) . '_grid_columns', 'option');
        return apply_filters('Municipio/Controller/Archive/GridColumnClass', $gridSize, $postType);
    }

    /**
     * Get the link to this page, without any query parameters
     *
     * @param   string  $postType   The current post type
     *
     * @return string
     */
    public function getPostTypeArchiveLink($postType) {
        return get_post_type_archive_link($postType);
    }

    /**
     * Determines if the reset button should show or not.
     *
     * @return boolean
     */
    public function showFilterReset($queryParams) : bool
    {
        return !empty(
            array_filter(
                (array) $queryParams
            )
        );
    }

    /**
     * Determines if the date input toggle should default to show or not.
     *
     * @return boolean
     */
    public function showDatePickers($queryParams) : bool
    {
        //From field
        if(isset($queryParams->from) && !empty($queryParams->from)) {
            return true;
        }

        //To field
        if(isset($queryParams->to) && !empty($queryParams->to)) {
            return true;
        }

        return false;
    }

    /**
     * Get the archive title
     *
     * @return string
     */
    protected function getArchiveTitle($postType)
    {
        return (string) \apply_filters(
            'Municipio/Controller/Archive/getArchiveTitle',
            get_field('archive_'. $postType .'_title', 'options')
        );
    }

    /**
     * Get a list of terms to display on each inlay
     *
     * @param integer $postId           The post identifier
     * @param boolean $includeLink      If a link should be included or not
     * @return array                    A array of terms to display
     */
    protected function getPostTerms($postId, $includeLink = false)
    {
        $taxonomies = get_field('archive_'. get_post_type($postId) .'_post_taxonomy_display', 'options');

        $termsList = [];

        if(is_array($taxonomies) && !empty($taxonomies)) {
            foreach ($taxonomies as $taxonomy) {
                $terms = wp_get_post_terms($postId, $taxonomy);

                if (!empty($terms)) {
                    foreach ($terms as $term) {

                        $item = [];

                        $item['label'] = strtolower($term->name);

                        if($includeLink) {
                            $item['href'] = get_term_link($term->term_id);
                        }

                        $termsList[] = $item;
                    }
                }
            }
        }

        return \apply_filters('Municipio/Controller/Archive/getPostTerms', $termsList, $postId);
    }

    /**
     * Get pagination
     *
     * @return array    Pagination array with label and link
     */
    protected function getPagination($postType, $archiveBaseUrl, $wpQuery)
    {
        $numberOfPages = (int) ceil($wpQuery->max_num_pages) + 1;

        if ($numberOfPages > 1) {
            for ($i = 1; $i < $numberOfPages; $i++) {

                $href = $archiveBaseUrl . '?' . $this->setQueryString($i);

                $pagination[] = array(
                    'href' => $href,
                    'label' => (string) $i
                );

            }
        }

        return \apply_filters('Municipio/Controller/Archive/getPagination', $pagination);
    }

    /**
     * Of the pagination should show or no
     *
     * @return bool
     */
    protected function showPagination($postType, $archiveBaseUrl, $wpQuery) {

        $pagesArray = $this->getPagination($postType, $archiveBaseUrl, $wpQuery); 

        if(is_null($pagesArray)) {
            return false;
        }

        if(count($pagesArray) > 1) {
            return true; 
        }

        return false; 
    }

    /**
     * Build a query string with page numer
     *
     * @param integer $number
     * @return void
     */
    protected function setQueryString($number)
    {
        parse_str($_SERVER['QUERY_STRING'], $queryArgList);
        $queryArgList['paged'] = $number;
        $queryString = http_build_query($queryArgList) . "\n";

        return \apply_filters('Municipio/Controller/Archive/setQueryString', $queryString);
    }

    /**
     * Set default values for query parameters
     *
     * @return void
     */
    protected function setQueryParameters()
    {
        $queryParameters = [
            'search' =>  isset($_GET['s']) ? $_GET['s'] : '',
            'from' =>  isset($_GET['from']) ? $_GET['from'] : '',
            'to' =>  isset($_GET['to']) ? $_GET['to'] : ''
        ];

        //Include taxonomies (dynamic)
        $taxonomies = get_object_taxonomies($this->data['postType']);

        if(is_array($taxonomies) && !empty($taxonomies)) {
            foreach ($taxonomies as $taxonomy) {
                $queryParameters[$taxonomy] = isset($_GET[$taxonomy]) ? $_GET[$taxonomy] : '';
            }
        }

        return \apply_filters(
            'Municipio/Controller/Archive/setQueryParameters',
            (object) $queryParameters
        );
    }

    /**
     * Get taxonomy filters to show
     *
     * @param   string  $postType           The current post type
     * @return  array   $taxonomyObjects    Array containing selects with options
     */
    protected function getTaxonomyFilters($postType)
    {

        //Define storage point
        $taxonomyObjects = [];

        //Get active taxonomy filters
        $taxonomies = get_field('archive_' . $postType . '_post_filters_sidebar', 'options');

        if(is_array($taxonomies) && !empty($taxonomies)) {
            foreach ($taxonomies as $taxonomy) {

                //Fetch full object
                $taxonomy = get_taxonomy($taxonomy);

                //Bail if not found
                if($taxonomy === false) {
                    continue;
                }

                //Get terms
                $terms = get_terms(
                    array(
                        'taxonomy' => $taxonomy->name,
                        'hide_empty' => true
                    )
                );

                //Bail early if there isen't any options
                if(empty($terms)) {
                    continue;
                }

                //Reset options
                $options = [];

                //Fill options
                if(is_array($terms) && !empty($terms)) {
                    foreach($terms as $option) {
                        if(!empty($option->name)) {
                            $options[$option->slug] = ucfirst($option->name) . " (" . $option->count . ")";
                        }
                    }
                }

                //Data
                $taxonomyObject = [
                    'label' => (__("Select", 'municipio') . " " . strtolower($taxonomy->labels->singular_name)),
                    'required' => false,
                    'attributeList' => [
                        'type' => 'text',
                        'name' => $taxonomy->name
                    ],
                    'options' => $options
                ];

                if (isset($_GET[$taxonomy->name])) {
                    $taxonomyObject['preselected'] = $_GET[$taxonomy->name];
                }

                $taxonomyObjects[] = $taxonomyObject;
            }

        }

        return \apply_filters('Municipio/Controller/Archive/getTaxonomies', $taxonomyObjects);
    }

    /**
     * Get posts in expected format for each component.
     *
     * @param   string  $template  The template identifier
     *
     * @return  array   $items     Array of posts
     */
    public function getPosts($template) : array
    {
        $items = null;
        if (is_array($this->posts) && !empty($this->posts)) {

            if ($template == 'list') {
                $items = $this->getListItems($this->posts);
            } else {
                $items = $this->getArchiveItems($this->posts);
            }

            return \apply_filters('Municipio/Controller/Archive/getArchivePosts', $items);
        }

        return [];
    }

    /**
     * Prepare posts for general output
     *
     * @param   array $posts    The posts
     * @return  array           The posts - formatted
     */
    protected function getArchiveItems(array $posts) : array
    {
        $preparedPosts = [];

        if(is_array($posts) && !empty($posts)) {
            foreach ($posts as $post) {

                $post                   = \Municipio\Helper\Post::preparePostObject($post);
                $post->href             = $post->permalink;
                $post->excerpt          = $post->postExcerpt;
                $post->postDate         = \date('Y-m-d', strtotime($post->postDate));
                $post->postModified     = \date('Y-m-d', strtotime($post->postModified));
                $post->terms            = $this->getPostTerms($post->id);
                $post->termsUnlinked    = $this->getPostTerms($post->id, false);

                $preparedPosts[] = $post;
            }
        }

        return $preparedPosts;
    }

    /**
     * Prepare posts for list output
     *
     * @param   array $posts    The posts
     * @return  array           The posts - formatted
     */
    protected function getListItems(array $posts) : array
    {
        $preparedPosts = [
            'items' => [],
            'headings' => ['Title', 'Published', 'Updated']
        ];

        if(is_array($posts) && !empty($posts)) {
            foreach ($posts as $post) {

                $post           = \Municipio\Helper\Post::preparePostObject($post);
                $postDate       = \date('Y-m-d', strtotime($post->postDate));
                $postModified   = \date('Y-m-d', strtotime($post->postModified));

                $preparedPosts['items'][] =
                [
                    'id' => $post->id,
                    'href' => get_permalink($post->id),
                    'columns' => [
                        $post->postTitle,
                        $post->post_date = $postDate,
                        $post->post_modified = $postModified
                    ]
                ];
            }
        }

        return $preparedPosts;
    }
}
