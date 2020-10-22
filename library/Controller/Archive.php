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
        
        //Set default values to query parameters
        $this->data['queryParameters']          = $this->setQueryParameters();

        //Pagination
        $this->data['paginationList']           = $this->getPagination();
        $this->data['showPagination']           = $this->showPagination();
        $this->data['currentPage']              = $this->getCurrentPage();  

        //Filter options 
        $this->data['taxonomyFilters']          = $this->getTaxonomyFilters($postType);
        $this->data['filterPosition']           = $this->getFilterPosition($postType);
        $this->data['enableTextSearch']         = $this->enableTextSearch($postType);
        $this->data['enableDateFilter']         = $this->enableDateFilter($postType); 
        
        //Archive data
        $this->data['archiveTitle']             = $this->getArchiveTitle($postType);
        $this->data['archiveBaseUrl']           = $this->getPostTypeArchiveLink($postType); 
        $this->data['gridColumnClass']          = $this->getGridClass($postType); 

        //Display functions 
        $this->data['showFilterReset']          = $this->showFilterReset($this->data['queryParameters']); 
        $this->data['showDatePickers']          = $this->showDatePickers($this->data['queryParameters']);

        //Language
        $this->data['lang']['noResult']         = sprintf(__('No %s to show', 'municipio'), strtolower($this->data['postTypeDetails']->labels->archives)); 
        
    }

    public function showSidebarNavigation($postType) {
        return (bool) get_field('archive_' . sanitize_title($postType) . '_show_sidebar_navigation', 'option'); 
    }

    public function enableTextSearch($postType) {
        return (bool) in_array('text_search', (array) get_field('archive_' . sanitize_title($postType) . '_post_filters_header', 'options')); 
    }

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

    protected function getPostTerms($postID)
    {
        $terms = wp_get_post_terms($postID);
        $taxonomies = get_taxonomies('', 'names');
        $termsList = [];

        foreach ($taxonomies as $taxonomy) {
            $terms = wp_get_post_terms($postID, $taxonomy);

            if (!empty($terms)) {
                foreach ($terms as $term) {
                    $termsList[] = [
                        'label' => $term->name,
                        'href' => get_term_link($term->term_id)
                    ];
                }
            }
        }

        return \apply_filters('Municipio/Controller/Archive/getArchiveTitle', $termsList);
    }

    protected function getPagination()
    {
        global $wp_query;
        $pagination = [];
        $numberOfPages = $wp_query->max_num_pages + 1;
        $archiveUrl = get_post_type_archive_link($this->data['postType']);
        $href = '';
        $currentPage = (get_query_var('paged')) ? get_query_var('paged') : 1;

        if ($numberOfPages > 1) {
            for ($i = 1; $i < $numberOfPages; $i++) {
                $href = $archiveUrl . '?' . $this->setQueryString($i);
    
                $pagination[] = array(
                    'href' => $href,
                    'label' => (string) $i
                );
            }
        }
        
        return \apply_filters('Municipio/Controller/Archive/prepareSearchResultObject', $pagination);
    }

    protected function showPagination() {
        return (bool) empty($this->getPagination()); 
    }

    protected function setQueryString($number)
    {
        parse_str($_SERVER['QUERY_STRING'], $queryArgList);
        $queryArgList['paged'] = $number;
        $queryString = http_build_query($queryArgList) . "\n";

        return \apply_filters('Municipio/Controller/Archive/setQueryString', $queryString);
    }

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
                            $options[$option->slug] = $option->name; 
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
                $post->featuredImage    = $this->getFeaturedImage($post);
                $post->excerpt          = $post->postExcerpt;
                $post->postDate         = \date('Y-m-d', strtotime($post->postDate));
                $post->postModified     = \date('Y-m-d', strtotime($post->postModified));
                $post->terms            = $this->getPostTerms($post->id);
    
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









    //TODO: MOVE TO POST HELPER PREPARE
    protected function getFeaturedImage($post)
    {
        $featuredImageID = get_post_thumbnail_id();
        $featuredImageSRC = \get_the_post_thumbnail_url($post->id);
        $featuredImageAlt = get_post_meta($featuredImageID, '_wp_attachment_image_alt', true);
        $featuredImageTitle = get_the_title($featuredImageID);

        $featuredImage = [
            'src' => $featuredImageSRC ? $featuredImageSRC : null,
            'alt' => $featuredImageAlt ? $featuredImageAlt : null,
            'title' => $featuredImageTitle ? $featuredImageTitle : null
        ];

        return \apply_filters('Municipio/Controller/Archive/getFeaturedImage', $featuredImage);
    }

    //TODO: Remove?
    public function gridAlterColumns()
    {
        $gridRand = array();

        switch ($this->data['gridSize']) {
            case 12:
                $gridRand = array(
                    array(12)
                );
                break;

            case 6:
                $gridRand = array(
                    array(12),
                    array(6, 6),
                    array(6, 6)
                );
                break;

            case 4:
                $gridRand = array(
                    array(8, 4),
                    array(4, 4, 4),
                    array(4, 8)
                );
                break;

            case 3:
                $gridRand = array(
                    array(6, 3, 3),
                    array(3, 3, 3, 3),
                    array(3, 3, 6),
                    array(3, 3, 3, 3),
                    array(3, 6, 3)
                );
                break;

            default:
                $gridRand = array(
                    array(12)
                );
                break;
        }

        self::$randomGridBase = $gridRand;
    }

    //TODO: Remove? 
    public static function getColumnSize()
    {
        // Fallback if not set
        if (empty(self::$randomGridBase)) {
            return 'grid-md-' . self::$gridSize;
        }

        if (empty(self::$gridRow)) {
            self::$gridRow = self::$randomGridBase;
        }

        if (empty(self::$gridColumns)) {
            self::$gridColumns = self::$gridRow[0];
            array_shift(self::$gridRow);
        }

        $columnSize = 'grid-md-' . self::$gridColumns[0];
        array_shift(self::$gridColumns);

        return $columnSize;
    }
}
